<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ManagementFee;
use App\Models\ManagementFeeInvoice;
use App\Models\Setting;
use App\Services\ManagementFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagementFeeController extends Controller
{
    protected $managementFeeService;

    public function __construct(ManagementFeeService $managementFeeService)
    {
        $this->managementFeeService = $managementFeeService;
    }

    /**
     * Display management fee dashboard
     */
    public function index()
    {
        try {
            $stats = $this->managementFeeService->calculateTotalManagementFees();
            $currentQuarterStats = $this->managementFeeService->getQuarterlyInvoiceStats();
            $settings = $this->managementFeeService->getCurrentSettings();
            
            $recentInvoices = ManagementFeeInvoice::with(['apartment', 'owner'])
                ->latest()
                ->take(10)
                ->get();

            return view('management-fees.index', compact(
                'stats', 
                'currentQuarterStats', 
                'settings', 
                'recentInvoices'
            ));

        } catch (\Exception $e) {
            toast_error('Failed to load management fee dashboard: ' . $e->getMessage());
            return redirect()->route('dashboard');
        }
    }

    /**
     * Show management fee settings
     */
    public function settings()
    {
        $settings = $this->managementFeeService->getCurrentSettings();
        return view('management-fees.settings', compact('settings'));
    }

    /**
     * Update management fee settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'management_fee_ratio' => 'required|numeric|min:0|max:999.99',
            'sinking_fund_ratio' => 'required|numeric|min:0|max:999.99',
            'due_days' => 'required|integer|min:1|max:365',
            'late_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Update settings
                Setting::setValue('management_fee_ratio', $request->management_fee_ratio, 'decimal', 'management_fees');
                Setting::setValue('sinking_fund_ratio', $request->sinking_fund_ratio, 'decimal', 'management_fees');
                Setting::setValue('management_fee_due_days', $request->due_days, 'integer', 'management_fees');
                Setting::setValue('management_fee_late_fee_percentage', $request->late_fee_percentage, 'decimal', 'management_fees');
                Setting::setValue('management_fee_auto_generate', $request->has('auto_generate'), 'boolean', 'management_fees');

                // Update existing management fees if requested
                if ($request->has('update_existing')) {
                    $updatedCount = $this->managementFeeService->updateGlobalRatios(
                        $request->management_fee_ratio,
                        $request->sinking_fund_ratio
                    );
                    
                    toast_success("Settings updated successfully! {$updatedCount} apartments were updated with new ratios.");
                } else {
                    toast_success('Settings updated successfully! New ratios will apply to future apartments.');
                }
            });

            return redirect()->route('management-fees.settings');

        } catch (\Exception $e) {
            toast_error('Failed to update settings: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show management fees for a specific apartment
     */
    public function apartmentFees($apartmentId)
    {
        try {
            $breakdown = $this->managementFeeService->getApartmentFeeBreakdown($apartmentId);
            return view('management-fees.apartment-fees', $breakdown);

        } catch (\Exception $e) {
            toast_error('Failed to load apartment fees: ' . $e->getMessage());
            return redirect()->route('apartments.index');
        }
    }

    /**
     * Create management fee for apartment
     */
    public function createForApartment(Request $request, $apartmentId)
    {
        $request->validate([
            'management_fee_ratio' => 'nullable|numeric|min:0|max:999.99',
            'sinking_fund_ratio' => 'nullable|numeric|min:0|max:999.99',
        ]);

        try {
            $apartment = Apartment::findOrFail($apartmentId);
            
            $managementFee = $this->managementFeeService->createManagementFeeForApartment(
                $apartment,
                $request->management_fee_ratio,
                $request->sinking_fund_ratio
            );

            toast_success("Management fee created successfully for apartment {$apartment->number}!");
            return redirect()->route('management-fees.apartment-fees', $apartmentId);

        } catch (\Exception $e) {
            toast_error('Failed to create management fee: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Show quarterly invoices
     */
    public function quarterlyInvoices(Request $request)
    {
        $quarter = $request->get('quarter', ManagementFeeInvoice::getCurrentQuarter());
        $year = $request->get('year', now()->year);

        try {
            $stats = $this->managementFeeService->getQuarterlyInvoiceStats($quarter, $year);
            
            $invoices = ManagementFeeInvoice::forQuarter($quarter, $year)
                ->with(['apartment', 'owner', 'managementFee'])
                ->orderBy('apartment_id')
                ->paginate(20)
                ->withQueryString();

            return view('management-fees.quarterly-invoices', compact('stats', 'invoices', 'quarter', 'year'));

        } catch (\Exception $e) {
            toast_error('Failed to load quarterly invoices: ' . $e->getMessage());
            return redirect()->route('management-fees.index');
        }
    }

    /**
     * Generate quarterly invoices
     */
    public function generateQuarterlyInvoices(Request $request)
    {
        $request->validate([
            'quarter' => 'required|integer|min:1|max:4',
            'year' => 'required|integer|min:2020|max:2030',
            'apartment_id' => 'nullable|exists:apartments,id',
        ]);

        try {
            $invoices = $this->managementFeeService->generateQuarterlyInvoices(
                $request->quarter,
                $request->year,
                $request->apartment_id,
                Auth::id()
            );

            $message = $invoices->count() > 0 
                ? "Successfully generated {$invoices->count()} invoices for Q{$request->quarter} {$request->year}!"
                : "No new invoices were generated. Invoices may already exist for this period.";

            toast_success($message);
            return redirect()->route('management-fees.quarterly-invoices', [
                'quarter' => $request->quarter,
                'year' => $request->year
            ]);

        } catch (\Exception $e) {
            toast_error('Failed to generate invoices: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Manual entry for management fees and sinking funds
     */
    public function manualEntry(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'management_fee' => 'required|numeric|min:0',
            'sinking_fund' => 'required|numeric|min:0',
            'quarter' => 'required|integer|min:1|max:4',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $apartment = Apartment::with('currentOwner')->findOrFail($request->apartment_id);
                
                // Ensure apartment has valid area
                if (!$apartment->area || $apartment->area <= 0) {
                    throw new \Exception("Apartment {$apartment->number} must have a valid area (sq ft) to calculate management fees.");
                }

                // Check if invoice already exists for this quarter/year
                $existingInvoice = ManagementFeeInvoice::where('apartment_id', $apartment->id)
                    ->where('quarter', $request->quarter)
                    ->where('year', $request->year)
                    ->first();

                if ($existingInvoice) {
                    throw new \Exception("Invoice already exists for {$apartment->number} in Q{$request->quarter} {$request->year}");
                }

                // Use apartment's current area
                $apartmentArea = $apartment->area;

                // Create management fee invoice manually
                $invoice = ManagementFeeInvoice::create([
                    'invoice_number' => $this->generateInvoiceNumber($request->quarter, $request->year),
                    'apartment_id' => $apartment->id,
                    'owner_id' => $apartment->currentOwner->id ?? null,
                    'billing_period_start' => $this->getQuarterStartDate($request->quarter, $request->year),
                    'billing_period_end' => $this->getQuarterEndDate($request->quarter, $request->year),
                    'quarter' => $request->quarter,
                    'year' => $request->year,
                    'area_sqft' => $apartmentArea, // Use apartment's actual area
                    'management_fee_ratio' => $apartmentArea > 0 ? $request->management_fee / ($apartmentArea * 3) : 0, // Calculate back the ratio
                    'sinking_fund_ratio' => $apartmentArea > 0 ? $request->sinking_fund / ($apartmentArea * 3) : 0, // Calculate back the ratio
                    'quarterly_management_fee' => $request->management_fee,
                    'quarterly_sinking_fund' => $request->sinking_fund,
                    'total_amount' => $request->management_fee + $request->sinking_fund,
                    'late_fee' => 0,
                    'discount' => 0,
                    'net_total' => $request->management_fee + $request->sinking_fund,
                    'status' => 'pending',
                    'due_date' => now()->addDays(Setting::getValue('management_fee_due_days', 14)),
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),
                ]);

                toast_success("Manual invoice {$invoice->invoice_number} created successfully for {$apartment->number} (Area: {$apartmentArea} sq ft)!");
            });

            return redirect()->route('management-fees.quarterly-invoices', [
                'quarter' => $request->quarter,
                'year' => $request->year
            ]);

        } catch (\Exception $e) {
            toast_error('Failed to create manual entry: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Helper method to generate invoice number
     */
    private function generateInvoiceNumber($quarter, $year)
    {
        $prefix = 'AMC' . $year;
        $lastInvoice = ManagementFeeInvoice::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Helper method to get quarter start date
     */
    private function getQuarterStartDate($quarter, $year)
    {
        $months = [
            1 => '01-01',
            2 => '04-01',
            3 => '07-01',
            4 => '10-01'
        ];

        return \Carbon\Carbon::createFromFormat('Y-m-d', $year . '-' . $months[$quarter]);
    }

    /**
     * Helper method to get quarter end date
     */
    private function getQuarterEndDate($quarter, $year)
    {
        $months = [
            1 => '03-31',
            2 => '06-30',
            3 => '09-30',
            4 => '12-31'
        ];

        return \Carbon\Carbon::createFromFormat('Y-m-d', $year . '-' . $months[$quarter]);
    }

    /**
     * Show specific invoice
     */
    public function showInvoice($invoiceId)
    {
        try {
            $invoice = ManagementFeeInvoice::with(['apartment', 'owner', 'managementFee', 'createdBy'])
                ->findOrFail($invoiceId);

            return view('management-fees.invoice-details', compact('invoice'));

        } catch (\Exception $e) {
            toast_error('Invoice not found.');
            return redirect()->route('management-fees.quarterly-invoices');
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markInvoicePaid(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $invoice = ManagementFeeInvoice::findOrFail($invoiceId);
            
            if ($invoice->isPaid()) {
                toast_warning('Invoice is already marked as paid.');
                return back();
            }

            $invoice->markAsPaid($request->payment_method, $request->payment_reference);
            
            toast_success("Invoice {$invoice->invoice_number} marked as paid successfully!");
            return back();

        } catch (\Exception $e) {
            toast_error('Failed to mark invoice as paid: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Apply discount to invoice
     */
    public function applyDiscount(Request $request, $invoiceId)
    {
        $request->validate([
            'discount_amount' => 'required|numeric|min:0',
        ]);

        try {
            $invoice = ManagementFeeInvoice::findOrFail($invoiceId);
            
            if ($request->discount_amount > $invoice->total_amount) {
                toast_error('Discount amount cannot exceed the total invoice amount.');
                return back();
            }

            $invoice->applyDiscount($request->discount_amount);
            
            toast_success("Discount of {$request->discount_amount} applied successfully!");
            return back();

        } catch (\Exception $e) {
            toast_error('Failed to apply discount: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Process overdue invoices
     */
    public function processOverdueInvoices()
    {
        try {
            $processedCount = $this->managementFeeService->processOverdueInvoices();
            
            toast_success("Processed {$processedCount} overdue invoices and applied late fees.");
            return back();

        } catch (\Exception $e) {
            toast_error('Failed to process overdue invoices: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Export quarterly invoices
     */
    public function exportQuarterlyInvoices(Request $request)
    {
        $quarter = $request->get('quarter', ManagementFeeInvoice::getCurrentQuarter());
        $year = $request->get('year', now()->year);

        try {
            // This would implement Excel/PDF export
            // For now, return JSON data
            $invoices = ManagementFeeInvoice::forQuarter($quarter, $year)
                ->with(['apartment', 'owner'])
                ->get();

            $stats = $this->managementFeeService->getQuarterlyInvoiceStats($quarter, $year);

            return response()->json([
                'quarter_info' => $stats,
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            toast_error('Failed to export invoices: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Analytics dashboard for management fees
     */
    public function analytics()
    {
        try {
            $currentYear = now()->year;
            $quarters = [];
            
            for ($q = 1; $q <= 4; $q++) {
                $quarters[$q] = $this->managementFeeService->getQuarterlyInvoiceStats($q, $currentYear);
            }

            $totalStats = $this->managementFeeService->calculateTotalManagementFees();
            
            return view('management-fees.analytics', compact('quarters', 'totalStats', 'currentYear'));

        } catch (\Exception $e) {
            toast_error('Failed to load analytics: ' . $e->getMessage());
            return redirect()->route('management-fees.index');
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice($invoiceId)
    {
        try {
            $invoice = ManagementFeeInvoice::with(['apartment', 'owner', 'managementFee'])
                ->findOrFail($invoiceId);

            // Prepare data for the invoice template
            $data = $this->prepareInvoiceData($invoice);

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('management-fees.invoice-template', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download("management-fee-invoice-{$invoice->invoice_number}.pdf");

        } catch (\Exception $e) {
            toast_error('Failed to generate PDF: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Print invoice (HTML view optimized for printing)
     */
    public function printInvoice($invoiceId)
    {
        try {
            $invoice = ManagementFeeInvoice::with(['apartment', 'owner', 'managementFee'])
                ->findOrFail($invoiceId);

            // Prepare data for the invoice template
            $data = $this->prepareInvoiceData($invoice);

            return view('management-fees.invoice-template', $data);

        } catch (\Exception $e) {
            toast_error('Invoice not found.');
            return redirect()->route('management-fees.quarterly-invoices');
        }
    }

    /**
     * Prepare invoice data for template rendering
     */
    private function prepareInvoiceData($invoice)
    {
        // Management Corporation details
        $managementCorp = $invoice->apartment->managementCorporation ?? new \stdClass();
        $managementCorp->plan_number = '7538';
        $managementCorp->registration_number = 'CMA/CCU/2023/PVT/MC/1018';
        $managementCorp->address = 'No. 202/1, AVERIWATTA ROAD, HUNUPITIYA, WATTALA';
        $managementCorp->email = 'propertymanager.altezza@gmail.com';

        // Calculate previous outstanding and payments
        // This should be replaced with actual business logic
        $previousOutstanding = 258852.58; // Get from previous quarter's data
        $totalPayments = 144523.94; // Sum of payments received
        $currentOutstanding = max(0, $previousOutstanding - $totalPayments);
        
        $previousPeriodEnd = '1st JANUARY ' . $invoice->year;
        $paymentsAsOf = '25th MARCH ' . $invoice->year;

        // Bank details (should come from settings)
        $bankDetails = [
            'account_name' => 'THE MCCP NO. 7538 ALTEZZA APARTMENT',
            'account_number' => '035010047455',
            'account_type' => 'CURRENT ACCOUNT',
            'bank_name' => 'Hatton National Bank (7083)',
            'branch_name' => 'Wattala (035)',
            'swift_code' => 'HBLILKLX'
        ];

        return [
            'invoice' => $invoice,
            'managementCorp' => $managementCorp,
            'previousOutstanding' => $previousOutstanding,
            'totalPayments' => $totalPayments,
            'currentOutstanding' => $currentOutstanding,
            'previousPeriodEnd' => $previousPeriodEnd,
            'paymentsAsOf' => $paymentsAsOf,
            'bankDetails' => $bankDetails
        ];
    }
}
