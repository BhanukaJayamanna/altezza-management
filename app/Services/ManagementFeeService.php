<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\ManagementFee;
use App\Models\ManagementFeeInvoice;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Management Fee Service
 * Handles all business logic related to management fees and calculations
 */
class ManagementFeeService
{
    /**
     * Create management fee record for a new apartment
     */
    public function createManagementFeeForApartment(Apartment $apartment, $managementRatio = null, $sinkingRatio = null)
    {
        try {
            // Get current ratios from settings if not provided
            $managementRatio = $managementRatio ?? Setting::getValue('management_fee_ratio', 14.00);
            $sinkingRatio = $sinkingRatio ?? Setting::getValue('sinking_fund_ratio', 2.50);

            // Check if apartment already has an active management fee
            $existingFee = $apartment->currentManagementFee;
            if ($existingFee) {
                throw new \Exception("Apartment {$apartment->number} already has an active management fee record.");
            }

            // Validate apartment area
            if (!$apartment->area || $apartment->area <= 0) {
                throw new \Exception("Apartment {$apartment->number} must have a valid area (square feet) to calculate management fees.");
            }

            return DB::transaction(function () use ($apartment, $managementRatio, $sinkingRatio) {
                $managementFee = ManagementFee::createForApartment($apartment, $managementRatio, $sinkingRatio);
                
                Log::info("Management fee created for apartment {$apartment->number}", [
                    'apartment_id' => $apartment->id,
                    'management_fee_id' => $managementFee->id,
                    'total_quarterly_rental' => $managementFee->total_quarterly_rental
                ]);

                return $managementFee;
            });

        } catch (\Exception $e) {
            Log::error("Failed to create management fee for apartment {$apartment->number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update management fee ratios globally
     */
    public function updateGlobalRatios($managementRatio, $sinkingRatio)
    {
        try {
            return DB::transaction(function () use ($managementRatio, $sinkingRatio) {
                $updatedCount = ManagementFee::updateGlobalRatios($managementRatio, $sinkingRatio);
                
                Log::info("Global management fee ratios updated", [
                    'management_ratio' => $managementRatio,
                    'sinking_ratio' => $sinkingRatio,
                    'apartments_updated' => $updatedCount
                ]);

                return $updatedCount;
            });

        } catch (\Exception $e) {
            Log::error("Failed to update global management fee ratios: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate quarterly invoices for all apartments or specific apartment
     */
    public function generateQuarterlyInvoices($quarter = null, $year = null, $apartmentId = null, $createdBy = null)
    {
        try {
            $quarter = $quarter ?? ManagementFeeInvoice::getCurrentQuarter();
            $year = $year ?? now()->year;

            return DB::transaction(function () use ($quarter, $year, $apartmentId, $createdBy) {
                $invoices = ManagementFeeInvoice::createForQuarter($quarter, $year, $apartmentId, $createdBy);
                
                Log::info("Management fee invoices generated", [
                    'quarter' => $quarter,
                    'year' => $year,
                    'apartment_id' => $apartmentId,
                    'invoices_created' => $invoices->count()
                ]);

                return $invoices;
            });

        } catch (\Exception $e) {
            Log::error("Failed to generate quarterly invoices: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate total management fees for all apartments
     */
    public function calculateTotalManagementFees()
    {
        try {
            $stats = ManagementFee::active()
                ->selectRaw('
                    COUNT(*) as total_apartments,
                    SUM(area_sqft) as total_area,
                    SUM(quarterly_management_fee) as total_quarterly_management_fees,
                    SUM(quarterly_sinking_fund) as total_quarterly_sinking_funds,
                    SUM(total_quarterly_rental) as total_quarterly_rentals,
                    AVG(management_fee_ratio) as avg_management_ratio,
                    AVG(sinking_fund_ratio) as avg_sinking_ratio
                ')
                ->first();

            return [
                'total_apartments' => $stats->total_apartments ?? 0,
                'total_area_sqft' => $stats->total_area ?? 0,
                'quarterly_totals' => [
                    'management_fees' => $stats->total_quarterly_management_fees ?? 0,
                    'sinking_funds' => $stats->total_quarterly_sinking_funds ?? 0,
                    'total_rentals' => $stats->total_quarterly_rentals ?? 0,
                ],
                'annual_totals' => [
                    'management_fees' => ($stats->total_quarterly_management_fees ?? 0) * 4,
                    'sinking_funds' => ($stats->total_quarterly_sinking_funds ?? 0) * 4,
                    'total_rentals' => ($stats->total_quarterly_rentals ?? 0) * 4,
                ],
                'average_ratios' => [
                    'management_fee' => $stats->avg_management_ratio ?? 0,
                    'sinking_fund' => $stats->avg_sinking_ratio ?? 0,
                ]
            ];

        } catch (\Exception $e) {
            Log::error("Failed to calculate total management fees: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get invoice statistics for a specific quarter
     */
    public function getQuarterlyInvoiceStats($quarter = null, $year = null)
    {
        try {
            $quarter = $quarter ?? ManagementFeeInvoice::getCurrentQuarter();
            $year = $year ?? now()->year;

            $stats = ManagementFeeInvoice::forQuarter($quarter, $year)
                ->selectRaw('
                    COUNT(*) as total_invoices,
                    SUM(total_amount) as total_amount,
                    SUM(net_total) as total_net_amount,
                    SUM(late_fee) as total_late_fees,
                    SUM(discount) as total_discounts,
                    COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_invoices,
                    COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_invoices,
                    COUNT(CASE WHEN status = "overdue" THEN 1 END) as overdue_invoices,
                    SUM(CASE WHEN status = "paid" THEN net_total ELSE 0 END) as collected_amount,
                    SUM(CASE WHEN status != "paid" THEN net_total ELSE 0 END) as outstanding_amount
                ')
                ->first();

            return [
                'quarter' => $quarter,
                'year' => $year,
                'quarter_name' => "Q{$quarter} {$year}",
                'totals' => [
                    'invoices' => $stats->total_invoices ?? 0,
                    'amount' => $stats->total_amount ?? 0,
                    'net_amount' => $stats->total_net_amount ?? 0,
                    'late_fees' => $stats->total_late_fees ?? 0,
                    'discounts' => $stats->total_discounts ?? 0,
                ],
                'payment_status' => [
                    'paid' => $stats->paid_invoices ?? 0,
                    'pending' => $stats->pending_invoices ?? 0,
                    'overdue' => $stats->overdue_invoices ?? 0,
                ],
                'financial' => [
                    'collected' => $stats->collected_amount ?? 0,
                    'outstanding' => $stats->outstanding_amount ?? 0,
                    'collection_rate' => $stats->total_net_amount > 0 
                        ? round(($stats->collected_amount / $stats->total_net_amount) * 100, 2) 
                        : 0,
                ]
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get quarterly invoice stats: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process overdue invoices and apply late fees
     */
    public function processOverdueInvoices()
    {
        try {
            return DB::transaction(function () {
                $overdueInvoices = ManagementFeeInvoice::where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->get();

                $processedCount = 0;
                foreach ($overdueInvoices as $invoice) {
                    $invoice->update(['status' => 'overdue']);
                    $invoice->applyLateFee();
                    $processedCount++;
                }

                Log::info("Processed overdue management fee invoices", [
                    'processed_count' => $processedCount
                ]);

                return $processedCount;
            });

        } catch (\Exception $e) {
            Log::error("Failed to process overdue invoices: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get apartment management fee breakdown
     */
    public function getApartmentFeeBreakdown($apartmentId)
    {
        try {
            $apartment = Apartment::with('currentManagementFee')->findOrFail($apartmentId);
            $managementFee = $apartment->currentManagementFee;

            if (!$managementFee) {
                return [
                    'apartment' => $apartment,
                    'has_management_fee' => false,
                    'message' => 'No active management fee record found for this apartment.'
                ];
            }

            return [
                'apartment' => $apartment,
                'has_management_fee' => true,
                'management_fee' => $managementFee,
                'breakdown' => $managementFee->formatted_breakdown,
                'calculation_details' => $managementFee->calculation_details,
                'recent_invoices' => $apartment->managementFeeInvoices()
                    ->with('tenant')
                    ->latest()
                    ->take(5)
                    ->get()
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get apartment fee breakdown: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get current management fee settings
     */
    public function getCurrentSettings()
    {
        return [
            'management_fee_ratio' => Setting::getValue('management_fee_ratio', 14.00),
            'sinking_fund_ratio' => Setting::getValue('sinking_fund_ratio', 2.50),
            'billing_cycle' => Setting::getValue('management_fee_billing_cycle', 'quarterly'),
            'due_days' => Setting::getValue('management_fee_due_days', 30),
            'late_fee_percentage' => Setting::getValue('management_fee_late_fee_percentage', 5.00),
            'auto_generate' => Setting::getValue('management_fee_auto_generate', true),
            'currency_symbol' => Setting::getValue('management_fee_currency_symbol', 'LKR '),
        ];
    }

    /**
     * Validate management fee data
     */
    public function validateManagementFeeData($data)
    {
        $rules = [
            'management_fee_ratio' => 'required|numeric|min:0|max:999.99',
            'sinking_fund_ratio' => 'required|numeric|min:0|max:999.99',
            'area_sqft' => 'required|numeric|min:0.01',
        ];

        return validator($data, $rules);
    }
}
