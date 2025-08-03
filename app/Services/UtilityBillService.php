<?php

namespace App\Services;

use App\Models\UtilityMeter;
use App\Models\UtilityReading;
use App\Models\UtilityBill;
use App\Models\UtilityUnitPrice;
use App\Models\Apartment;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class UtilityBillService
{
    /**
     * Generate utility bills for a specific month and year
     */
    public function generateUtilityBills(int $month, int $year): array
    {
        $results = [
            'generated' => 0,
            'errors' => [],
            'bills' => []
        ];

        DB::beginTransaction();
        
        try {
            // Get all readings for the specified month/year
            $readings = UtilityReading::whereMonth('reading_date', $month)
                ->whereYear('reading_date', $year)
                ->with(['meter.apartment.tenants'])
                ->get();

            foreach ($readings as $reading) {
                $meter = $reading->meter;
                $apartment = $meter->apartment;
                
                // Get the current tenant for this apartment
                $tenant = $apartment->tenants()->first();
                if (!$tenant) {
                    $results['errors'][] = "No tenant found for apartment {$apartment->number}";
                    continue;
                }

                // Get unit price for this utility type and period
                $unitPrice = UtilityUnitPrice::getCurrentPrice(
                    $meter->type, 
                    Carbon::create($year, $month, 1)
                );

                if (!$unitPrice) {
                    $results['errors'][] = "No unit price found for {$meter->type} in {$month}/{$year}";
                    continue;
                }

                $period = sprintf('%02d/%04d', $month, $year);
                
                // Check if bill already exists
                $existingBill = UtilityBill::where('meter_id', $meter->id)
                    ->where('period', $period)
                    ->first();

                if ($existingBill) {
                    continue; // Skip if bill already exists
                }

                // Calculate bill amount
                $totalAmount = $reading->consumption * $unitPrice;
                
                // Create utility bill
                $bill = UtilityBill::create([
                    'tenant_id' => $tenant->id,
                    'apartment_id' => $apartment->id,
                    'meter_id' => $meter->id,
                    'reading_id' => $reading->id,
                    'type' => $meter->type,
                    'period' => $period,
                    'month' => $month,
                    'year' => $year,
                    'units_used' => $reading->consumption,
                    'price_per_unit' => $unitPrice,
                    'total_amount' => $totalAmount,
                    'status' => 'unpaid',
                    'due_date' => Carbon::create($year, $month)->addMonth()->day(10), // Due 10th of next month
                    'paid_amount' => 0
                ]);

                // Automatically create corresponding invoice
                $invoice = $this->createInvoiceFromUtilityBill($bill);
                $bill->update(['invoice_id' => $invoice->id]);

                $results['generated']++;
                $results['bills'][] = $bill;
            }

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            $results['errors'][] = "Database error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Calculate consumption and amount for a reading
     */
    public function calculateReadingAmount(UtilityReading $reading): void
    {
        $meter = $reading->meter;
        
        // Get previous reading
        $previousReading = UtilityReading::where('meter_id', $meter->id)
            ->where('reading_date', '<', $reading->reading_date)
            ->orderBy('reading_date', 'desc')
            ->first();

        $reading->previous_reading = $previousReading ? $previousReading->current_reading : 0;
        $reading->consumption = $reading->current_reading - $reading->previous_reading;
        
        // Get unit price for calculation
        $unitPrice = UtilityUnitPrice::getCurrentPrice($meter->type, $reading->reading_date);
        $reading->amount = $reading->consumption * ($unitPrice ?? $meter->rate_per_unit);
        
        $reading->save();
    }

    /**
     * Get usage analytics for a tenant
     */
    public function getTenantUsageAnalytics(int $tenantId, int $months = 12): array
    {
        $bills = UtilityBill::where('tenant_id', $tenantId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take($months)
            ->get();

        $analytics = [
            'total_bills' => $bills->count(),
            'total_amount' => $bills->sum('total_amount'),
            'paid_amount' => $bills->sum('paid_amount'),
            'outstanding_amount' => $bills->where('status', '!=', 'paid')->sum('total_amount'),
            'usage_by_type' => [],
            'monthly_usage' => []
        ];

        // Group by utility type
        foreach (['electricity', 'water', 'gas'] as $type) {
            $typeBills = $bills->where('type', $type);
            $analytics['usage_by_type'][$type] = [
                'total_units' => $typeBills->sum('units_used'),
                'total_amount' => $typeBills->sum('total_amount'),
                'average_rate' => $typeBills->avg('price_per_unit')
            ];
        }

        // Monthly breakdown
        foreach ($bills as $bill) {
            $monthKey = $bill->period;
            if (!isset($analytics['monthly_usage'][$monthKey])) {
                $analytics['monthly_usage'][$monthKey] = [
                    'electricity' => 0,
                    'water' => 0,
                    'gas' => 0,
                    'total_amount' => 0
                ];
            }
            
            $analytics['monthly_usage'][$monthKey][$bill->type] = $bill->units_used;
            $analytics['monthly_usage'][$monthKey]['total_amount'] += $bill->total_amount;
        }

        return $analytics;
    }

    /**
     * Get overdue bills report
     */
    public function getOverdueBillsReport(): array
    {
        $overdueBills = UtilityBill::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->with(['tenant', 'apartment', 'meter'])
            ->get();

        $report = [
            'total_overdue' => $overdueBills->count(),
            'total_amount' => $overdueBills->sum('total_amount'),
            'bills_by_tenant' => [],
            'bills_by_type' => []
        ];

        // Group by tenant
        foreach ($overdueBills->groupBy('tenant_id') as $tenantId => $bills) {
            $tenant = $bills->first()->tenant;
            $report['bills_by_tenant'][] = [
                'tenant' => $tenant,
                'bills_count' => $bills->count(),
                'total_amount' => $bills->sum('total_amount'),
                'oldest_bill_date' => $bills->min('due_date')
            ];
        }

        // Group by utility type
        foreach (['electricity', 'water', 'gas'] as $type) {
            $typeBills = $overdueBills->where('type', $type);
            $report['bills_by_type'][$type] = [
                'count' => $typeBills->count(),
                'amount' => $typeBills->sum('total_amount')
            ];
        }

        return $report;
    }

    /**
     * Mark multiple bills as overdue
     */
    public function markOverdueBills(): int
    {
        return UtilityBill::where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);
    }

    /**
     * Create an invoice from a utility bill
     */
    public function createInvoiceFromUtilityBill(UtilityBill $bill): Invoice
    {
        // Generate unique invoice number
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);
        
        // Create billing period dates
        $billingStart = Carbon::create($bill->year, $bill->month, 1);
        $billingEnd = $billingStart->copy()->endOfMonth();
        
        // Create line items for detailed breakdown
        $lineItems = [
            [
                'description' => ucfirst($bill->type) . ' usage for ' . $billingStart->format('F Y'),
                'quantity' => $bill->units_used,
                'unit' => $this->getUtilityUnit($bill->type),
                'rate' => $bill->price_per_unit,
                'amount' => $bill->total_amount
            ]
        ];
        
        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'type' => 'utility',
            'apartment_id' => $bill->apartment_id,
            'tenant_id' => $bill->tenant_id,
            'lease_id' => $bill->apartment->currentLease?->id,
            'utility_bill_id' => $bill->id,
            'billing_period_start' => $billingStart,
            'billing_period_end' => $billingEnd,
            'amount' => $bill->total_amount,
            'late_fee' => 0,
            'discount' => 0,
            'total_amount' => $bill->total_amount,
            'due_date' => $bill->due_date,
            'status' => 'pending',
            'description' => ucfirst($bill->type) . ' utility bill for ' . $billingStart->format('F Y') . ' - Apartment ' . $bill->apartment->number,
            'line_items' => $lineItems,
            'created_by' => 1 // System generated
        ]);
    }

    /**
     * Get utility unit display name
     */
    private function getUtilityUnit(string $type): string
    {
        return match($type) {
            'electricity' => 'kWh',
            'water' => 'gallons',
            'gas' => 'cubic feet',
            default => 'units'
        };
    }
}
