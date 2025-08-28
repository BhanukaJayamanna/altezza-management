<?php

namespace App\Http\Controllers;

use App\Models\UtilityBill;
use App\Models\UtilityBillPayment;
use App\Services\UtilityBillService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UtilityBillController extends Controller
{
    protected $utilityBillService;

    public function __construct(UtilityBillService $utilityBillService)
    {
        $this->utilityBillService = $utilityBillService;
    }

    public function index(Request $request): View
    {
        $query = UtilityBill::with([
            'owner', 
            'apartment.currentLease.owner', 
            'meter'
        ]);

        // Filter by apartment
        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by utility type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by owner
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Filter by period
        if ($request->filled('period')) {
            $query->where('billing_period', 'like', $request->period . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Check for overdue bills
        if ($request->has('overdue')) {
            $query->where('status', '!=', 'paid')
                  ->where('due_date', '<', now());
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total_bills' => UtilityBill::count(),
            'paid_bills' => UtilityBill::where('status', 'paid')->count(),
            'pending_bills' => UtilityBill::where('status', 'unpaid')->count(),
            'overdue_bills' => UtilityBill::where('status', '!=', 'paid')
                ->where('due_date', '<', now())->count(),
        ];

        // Get filter options
        $owners = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'owner');
        })->orderBy('name')->get();

        $apartments = \App\Models\Apartment::orderBy('number')->get();

        return view('utilities.bills.index', compact('bills', 'owners', 'apartments', 'stats'));
    }

    public function create(): View
    {
        $apartments = \App\Models\Apartment::with('currentLease.owner')->orderBy('number')->get();
        $meters = \App\Models\UtilityMeter::where('status', 'active')->with('apartment')->orderBy('apartment_id')->orderBy('type')->get();
        
        return view('utilities.bills.create', compact('apartments', 'meters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'meter_id' => 'nullable|exists:utility_meters,id',
            'billing_period' => 'required|date',
            'usage_amount' => 'required|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);

        // Get current owner from apartment
        $apartment = \App\Models\Apartment::with('currentLease.owner')->find($validated['apartment_id']);
        if (!$apartment || !$apartment->currentLease) {
            return back()->withErrors(['apartment_id' => 'Selected apartment has no active lease.'])->withInput();
        }

        // Parse billing period and get utility type from meter
        $billingDate = Carbon::parse($validated['billing_period']);
        $meter = $validated['meter_id'] ? \App\Models\UtilityMeter::find($validated['meter_id']) : null;
        
        // Prepare data for database
        $billData = [
            'owner_id' => $apartment->currentLease->owner->id,
            'apartment_id' => $validated['apartment_id'],
            'meter_id' => $validated['meter_id'],
            'reading_id' => null, // Manual bill, no reading
            'type' => $meter ? $meter->type : 'electricity', // Default to electricity if no meter
            'period' => $billingDate->format('m/Y'), // Format as MM/YYYY
            'month' => $billingDate->month,
            'year' => $billingDate->year,
            'units_used' => $validated['usage_amount'],
            'price_per_unit' => $validated['rate_per_unit'],
            'total_amount' => $validated['total_amount'],
            'status' => 'unpaid',
            'due_date' => $validated['due_date'],
            'paid_amount' => 0,
            'notes' => $validated['notes']
        ];

        $bill = UtilityBill::create($billData);

        toast_success('Utility bill created successfully!');
        return redirect()->route('utility-bills.show', $bill);
    }

    public function show(UtilityBill $bill): View
    {
        $bill->load(['owner', 'apartment', 'meter', 'reading', 'payments.recordedBy']);

        return view('utilities.bills.show', compact('bill'));
    }

    public function edit(UtilityBill $bill)
    {
        // Only allow editing of pending bills
        if ($bill->status === 'paid') {
            toast_error('Cannot edit a paid bill.');
            return redirect()->route('utility-bills.show', $bill);
        }

        $apartments = \App\Models\Apartment::with('currentLease.owner')->orderBy('number')->get();
        $meters = \App\Models\UtilityMeter::where('status', 'active')->with('apartment')->orderBy('apartment_id')->orderBy('type')->get();
        
        return view('utilities.bills.edit', compact('bill', 'apartments', 'meters'));
    }

    public function update(Request $request, UtilityBill $bill): RedirectResponse
    {
        // Only allow updating of pending bills
        if ($bill->status === 'paid') {
            toast_error('Cannot update a paid bill.');
            return redirect()->route('utility-bills.show', $bill);
        }

        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'meter_id' => 'nullable|exists:utility_meters,id',
            'billing_period' => 'required|date',
            'usage_amount' => 'required|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Get current owner from apartment
        $apartment = \App\Models\Apartment::with('currentLease.owner')->find($validated['apartment_id']);
        if (!$apartment || !$apartment->currentLease) {
            return back()->withErrors(['apartment_id' => 'Selected apartment has no active lease.'])->withInput();
        }

        // Parse billing period and get utility type from meter
        $billingDate = Carbon::parse($validated['billing_period']);
        $meter = $validated['meter_id'] ? \App\Models\UtilityMeter::find($validated['meter_id']) : null;
        
        // Prepare data for database
        $billData = [
            'owner_id' => $apartment->currentLease->owner->id,
            'apartment_id' => $validated['apartment_id'],
            'meter_id' => $validated['meter_id'],
            'type' => $meter ? $meter->type : $bill->type, // Keep existing type if no meter
            'period' => $billingDate->format('m/Y'), // Format as MM/YYYY
            'month' => $billingDate->month,
            'year' => $billingDate->year,
            'units_used' => $validated['usage_amount'],
            'price_per_unit' => $validated['rate_per_unit'],
            'total_amount' => $validated['total_amount'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes']
        ];

        $bill->update($billData);

        toast_success('Utility bill updated successfully!');
        return redirect()->route('utility-bills.show', $bill);
    }

    public function markAsPaid(UtilityBill $utilityBill): View
    {
        return view('utilities.bills.mark-paid', compact('utilityBill'));
    }

    public function processPayment(Request $request, UtilityBill $utilityBill): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $utilityBill->remaining_amount,
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card,online',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $utilityBill->markAsPaid(
                $validated['amount'],
                $validated['payment_method'],
                $validated['reference_number']
            );

            // Add notes to the payment record
            if (!empty($validated['notes'])) {
                $payment = $utilityBill->payments()->latest()->first();
                $payment->update(['notes' => $validated['notes']]);
            }

            toast_success('Payment recorded successfully!');
            return redirect()->route('utility-bills.show', $utilityBill);

        } catch (\Exception $e) {
            return back()->withErrors(['amount' => 'Failed to process payment: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function ownerIndex(Request $request): View
    {
        $owner = Auth::user();
        
        $query = UtilityBill::where('owner_id', $owner->id)
            ->with(['apartment', 'meter', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by utility type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get analytics
        $analytics = $this->utilityBillService->getOwnerUsageAnalytics($owner->id, 12);

        return view('utilities.bills.owner-index', compact('bills', 'analytics'));
    }

    public function ownerShow(UtilityBill $utilityBill): View
    {
        // Ensure owner can only view their own bills
        if ($utilityBill->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to utility bill.');
        }

        $utilityBill->load(['apartment', 'meter', 'reading', 'payments']);

        return view('utilities.bills.owner-show', compact('utilityBill'));
    }

    public function downloadPdf(UtilityBill $utilityBill)
    {
        // Ensure owner can only download their own bills
        // Check if user is a owner and can only access their own bills  
        if ($utilityBill->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to utility bill.');
        }

        $utilityBill->load(['owner', 'apartment', 'meter', 'reading']);

        // For now, return a view that can be printed as PDF
        // Later you can integrate with a PDF library like DomPDF
        return view('utilities.bills.pdf', compact('utilityBill'));
    }

    public function analytics(): View
    {
        // Overview statistics
        $stats = [
            'total_bills' => UtilityBill::count(),
            'total_amount' => UtilityBill::sum('total_amount'),
            'paid_amount' => UtilityBill::sum('paid_amount'),
            'unpaid_bills' => UtilityBill::where('status', '!=', 'paid')->count(),
            'overdue_bills' => UtilityBill::where('status', '!=', 'paid')
                                         ->where('due_date', '<', now())->count()
        ];

        // Monthly collection report
        $monthlyCollection = UtilityBill::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            SUM(total_amount) as total_billed,
            SUM(paid_amount) as total_collected,
            COUNT(*) as bills_count
        ')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->take(12)
        ->get();

        // Usage by utility type
        $usageByType = UtilityBill::selectRaw('
            type,
            SUM(units_used) as total_units,
            SUM(total_amount) as total_amount,
            AVG(price_per_unit) as avg_rate,
            COUNT(*) as bills_count
        ')
        ->groupBy('type')
        ->get();

        // Top consumers
        $topConsumers = UtilityBill::selectRaw('
            owner_id,
            SUM(total_amount) as total_amount,
            SUM(units_used) as total_units,
            COUNT(*) as bills_count
        ')
        ->with('owner')
        ->groupBy('owner_id')
        ->orderBy('total_amount', 'desc')
        ->take(10)
        ->get();

        // Overdue bills report
        $overdueReport = $this->utilityBillService->getOverdueBillsReport();

        return view('utilities.bills.analytics', compact(
            'stats', 'monthlyCollection', 'usageByType', 'topConsumers', 'overdueReport'
        ));
    }

    public function markOverdue(): RedirectResponse
    {
        $count = $this->utilityBillService->markOverdueBills();

        toast_success("Marked {$count} bills as overdue!");
        return back();
    }

    public function generateBills(): View
    {
        return view('utilities.bills.generate');
    }

    public function processGenerateBills(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1)
        ]);

        $results = $this->utilityBillService->generateUtilityBills(
            $validated['month'], 
            $validated['year']
        );

        if (!empty($results['errors'])) {
            toast_warning("Generated {$results['generated']} bills with errors: " . implode(', ', $results['errors']));
            return redirect()->route('utility-bills.index');
        }

        toast_success("Successfully generated {$results['generated']} utility bills for {$validated['month']}/{$validated['year']}!");
        return redirect()->route('utility-bills.index');
    }

    public function destroy(UtilityBill $bill): RedirectResponse
    {
        // Only allow deletion of unpaid bills with no payments
        if ($bill->status !== 'unpaid' || $bill->payments()->count() > 0) {
            toast_error('Cannot delete bills that have payments or are not unpaid.');
            return redirect()->route('utility-bills.index');
        }

        $bill->delete();

        toast_success('Utility bill deleted successfully!');
        return redirect()->route('utility-bills.index');
    }

    public function export(Request $request)
    {
        $query = UtilityBill::with(['apartment', 'meter', 'payments']);

        // Apply filters
        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('billing_period_from')) {
            $query->where('billing_period', '>=', $request->billing_period_from);
        }

        if ($request->filled('billing_period_to')) {
            $query->where('billing_period', '<=', $request->billing_period_to);
        }

        $bills = $query->orderBy('billing_period', 'desc')->get();

        $filename = 'utility_bills_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bills) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Bill ID',
                'Apartment',
                'Meter',
                'Billing Period',
                'Usage Amount',
                'Rate Per Unit',
                'Total Amount',
                'Due Date',
                'Status',
                'Amount Paid',
                'Payment Date',
                'Notes'
            ]);

            // Data
            foreach ($bills as $bill) {
                $paidAmount = $bill->payments->sum('amount');
                $lastPaymentDate = $bill->payments->isNotEmpty() 
                    ? $bill->payments->sortByDesc('payment_date')->first()->payment_date->format('Y-m-d')
                    : '';

                fputcsv($file, [
                    $bill->id,
                    $bill->apartment->number . ' - ' . $bill->apartment->type,
                    $bill->meter ? $bill->meter->type . ' (' . $bill->meter->meter_number . ')' : 'Manual Bill',
                    $bill->billing_period,
                    number_format($bill->usage_amount, 2),
                    '$' . number_format($bill->rate_per_unit, 4),
                    '$' . number_format($bill->total_amount, 2),
                    $bill->due_date->format('Y-m-d'),
                    ucfirst($bill->status),
                    '$' . number_format($paidAmount, 2),
                    $lastPaymentDate,
                    $bill->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate invoice from utility bill
     */
    public function generateInvoice(UtilityBill $utilityBill): RedirectResponse
    {
        try {
            // Check if invoice already exists
            if ($utilityBill->invoice_id) {
                toast_warning('Invoice already exists for this utility bill.');
                return redirect()->route('utility-bills.show', $utilityBill);
            }

            // Create invoice using the service
            $utilityBillService = app(\App\Services\UtilityBillService::class);
            $invoice = $utilityBillService->createInvoiceFromUtilityBill($utilityBill);
            
            // Update utility bill with invoice reference
            $utilityBill->update(['invoice_id' => $invoice->id]);

            toast_success("Invoice {$invoice->invoice_number} generated successfully!");
            return redirect()->route('invoices.show', $invoice);

        } catch (\Exception $e) {
            toast_error('Failed to generate invoice: ' . $e->getMessage());
            return redirect()->route('utility-bills.show', $utilityBill);
        }
    }

    /**
     * Bulk generate invoices for multiple utility bills
     */
    public function bulkGenerateInvoices(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bill_ids' => 'required|array',
            'bill_ids.*' => 'exists:utility_bills,id'
        ]);

        $bills = UtilityBill::whereIn('id', $validated['bill_ids'])
            ->whereNull('invoice_id')
            ->with(['apartment.currentLease', 'owner'])
            ->get();

        if ($bills->isEmpty()) {
            toast_warning('No eligible utility bills found or all selected bills already have invoices.');
            return redirect()->route('utility-bills.index');
        }

        $utilityBillService = app(\App\Services\UtilityBillService::class);
        $generated = 0;
        $errors = [];

        foreach ($bills as $bill) {
            try {
                $invoice = $utilityBillService->createInvoiceFromUtilityBill($bill);
                $bill->update(['invoice_id' => $invoice->id]);
                $generated++;
            } catch (\Exception $e) {
                $errors[] = "Bill {$bill->id}: " . $e->getMessage();
            }
        }

        if ($generated > 0) {
            if (empty($errors)) {
                toast_success("Successfully generated {$generated} invoices!");
            } else {
                toast_warning("Generated {$generated} invoices with some errors: " . implode(', ', array_slice($errors, 0, 3)));
            }
        } else {
            toast_error('Failed to generate any invoices. Errors: ' . implode(', ', $errors));
        }

        return redirect()->route('utility-bills.index');
    }
}
