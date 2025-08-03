<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Lease;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['apartment', 'tenant', 'payments']);
        
        // If user is a tenant, filter to only their invoices
        if (Auth::user()->role === 'tenant') {
            $tenantProfile = Auth::user()->tenant;
            if ($tenantProfile) {
                $query->where('tenant_id', $tenantProfile->id);
            } else {
                // If no tenant profile exists, return empty collection
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by month/year
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('due_date', $request->year)
                  ->whereMonth('due_date', $request->month);
        }

        // Search by invoice number or tenant name (admin/manager only)
        if ($request->filled('search')) {
            if (in_array(Auth::user()->role, ['admin', 'manager'])) {
                $query->where(function($q) use ($request) {
                    $q->where('invoice_number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('tenant', function($tenantQuery) use ($request) {
                          $tenantQuery->where('name', 'like', '%' . $request->search . '%');
                      })
                      ->orWhereHas('apartment', function($aptQuery) use ($request) {
                          $aptQuery->where('number', 'like', '%' . $request->search . '%');
                      });
                });
            } else {
                // For tenants, only search by invoice number
                $query->where('invoice_number', 'like', '%' . $request->search . '%');
            }
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Get summary statistics with same filtering
        $summaryQuery = Invoice::query();
        if (Auth::user()->role === 'tenant') {
            $tenantProfile = Auth::user()->tenant;
            if ($tenantProfile) {
                $summaryQuery->where('tenant_id', $tenantProfile->id);
            } else {
                $summaryQuery->whereRaw('1 = 0');
            }
        }
        
        $totalAmount = $summaryQuery->sum('total_amount');
        $paidAmount = $summaryQuery->where('status', 'paid')->sum('total_amount');
        $pendingAmount = $summaryQuery->where('status', 'pending')->sum('total_amount');
        $overdueAmount = $summaryQuery->where('status', 'overdue')->sum('total_amount');

        // Choose appropriate view based on user role
        $viewName = Auth::user()->role === 'tenant' ? 'invoices.tenant-index' : 'invoices.index';
        
        return view($viewName, compact(
            'invoices', 'totalAmount', 'paidAmount', 'pendingAmount', 'overdueAmount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apartments = Apartment::with('tenant')->where('status', 'occupied')->orderBy('number')->get();
        $tenants = User::whereHas('roles', function($query) {
            $query->where('name', 'tenant');
        })->orderBy('name')->get();
        $leases = Lease::with(['apartment', 'tenant'])->where('status', 'active')->orderBy('lease_number')->get();
        
        return view('invoices.create', compact('apartments', 'tenants', 'leases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'tenant_id' => 'required|exists:users,id',
            'lease_id' => 'nullable|exists:leases,id',
            'type' => 'required|in:rent,utility,maintenance,other',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after:billing_period_start',
        ]);

        // Calculate total amount
        $lateFee = $validated['late_fee'] ?? 0;
        $discount = $validated['discount'] ?? 0;
        
        $totalAmount = $validated['amount'] + $lateFee - $discount;

        // Set billing period if not provided
        $billingStart = $validated['billing_period_start'] ?? Carbon::parse($validated['due_date'])->startOfMonth();
        $billingEnd = $validated['billing_period_end'] ?? Carbon::parse($validated['due_date'])->endOfMonth();

        // Generate unique invoice number
        $invoiceNumber = $this->generateInvoiceNumber();

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'apartment_id' => $validated['apartment_id'],
            'tenant_id' => $validated['tenant_id'],
            'lease_id' => $validated['lease_id'] ?? null,
            'type' => $validated['type'],
            'status' => 'pending',
            'due_date' => $validated['due_date'],
            'billing_period_start' => $billingStart,
            'billing_period_end' => $billingEnd,
            'amount' => $validated['amount'],
            'late_fee' => $lateFee,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'description' => $validated['description'],
            'created_by' => Auth::id(),
        ]);

        toast_success('Invoice created successfully!');
        return redirect()->route('invoices.show', $invoice);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['apartment', 'tenant', 'payments', 'createdBy']);
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        // Only allow editing of pending invoices
        if ($invoice->status !== 'pending') {
            toast_error('Only pending invoices can be edited.');
            return redirect()->route('invoices.show', $invoice);
        }

        $apartments = Apartment::with('tenant')->where('status', 'occupied')->orderBy('number')->get();
        $tenants = User::whereHas('roles', function($query) {
            $query->where('name', 'tenant');
        })->orderBy('name')->get();
        $leases = Lease::with(['apartment', 'tenant'])->where('status', 'active')->orderBy('lease_number')->get();
        
        return view('invoices.edit', compact('invoice', 'apartments', 'tenants', 'leases'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Only allow updating of pending invoices
        if ($invoice->status !== 'pending') {
            toast_error('Only pending invoices can be updated.');
            return redirect()->route('invoices.show', $invoice);
        }

        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'tenant_id' => 'required|exists:users,id',
            'lease_id' => 'nullable|exists:leases,id',
            'type' => 'required|in:rent,utility,maintenance,other',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'billing_period_start' => 'nullable|date',
            'billing_period_end' => 'nullable|date|after:billing_period_start',
        ]);

        // Calculate total amount
        $lateFee = $validated['late_fee'] ?? 0;
        $discount = $validated['discount'] ?? 0;
        
        $totalAmount = $validated['amount'] + $lateFee - $discount;

        // Set billing period if not provided
        $billingStart = $validated['billing_period_start'] ?? $invoice->billing_period_start;
        $billingEnd = $validated['billing_period_end'] ?? $invoice->billing_period_end;

        $invoice->update([
            'apartment_id' => $validated['apartment_id'],
            'tenant_id' => $validated['tenant_id'],
            'lease_id' => $validated['lease_id'] ?? null,
            'type' => $validated['type'],
            'due_date' => $validated['due_date'],
            'billing_period_start' => $billingStart,
            'billing_period_end' => $billingEnd,
            'amount' => $validated['amount'],
            'late_fee' => $lateFee,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'description' => $validated['description'],
        ]);

        toast_success('Invoice updated successfully!');
        return redirect()->route('invoices.show', $invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Only allow deletion of pending invoices with no payments
        if ($invoice->status !== 'pending' || $invoice->payments->count() > 0) {
            toast_error('Only pending invoices without payments can be deleted.');
            return redirect()->route('invoices.show', $invoice);
        }

        $invoice->delete();

        toast_success('Invoice deleted successfully!');
        return redirect()->route('invoices.index');
    }

    /**
     * Generate monthly rent invoices for all occupied apartments
     */
    public function generateMonthlyRent(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'due_day' => 'required|integer|between:1,31',
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        $dueDay = $validated['due_day'];

        // Get all occupied apartments
        $apartments = Apartment::with(['tenant', 'tenantProfile'])
            ->where('status', 'occupied')
            ->whereNotNull('tenant_id')
            ->whereNotNull('rent_amount')
            ->get();

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($apartments as $apartment) {
            try {
                // Check if invoice already exists for this month
                $existingInvoice = Invoice::where('apartment_id', $apartment->id)
                    ->where('type', 'rent')
                    ->whereYear('due_date', $year)
                    ->whereMonth('due_date', $month)
                    ->first();

                if ($existingInvoice) {
                    $skipped++;
                    continue;
                }

                // Create due date
                $dueDate = Carbon::create($year, $month, min($dueDay, Carbon::create($year, $month)->daysInMonth));

                // Generate invoice
                $invoiceNumber = $this->generateInvoiceNumber();

                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'apartment_id' => $apartment->id,
                    'tenant_id' => $apartment->tenant_id,
                    'type' => 'rent',
                    'status' => 'pending',
                    'due_date' => $dueDate,
                    'billing_period_start' => Carbon::create($year, $month, 1),
                    'billing_period_end' => Carbon::create($year, $month)->endOfMonth(),
                    'amount' => $apartment->rent_amount,
                    'total_amount' => $apartment->rent_amount,
                    'description' => "Monthly rent for {$apartment->number} - " . Carbon::create($year, $month)->format('F Y'),
                    'created_by' => Auth::id(),
                ]);

                $created++;
            } catch (\Exception $e) {
                $errors[] = "Error creating invoice for apartment {$apartment->number}: " . $e->getMessage();
            }
        }

        $message = "Generated {$created} invoices";
        if ($skipped > 0) {
            $message .= ", skipped {$skipped} existing";
        }
        if (count($errors) > 0) {
            $message .= ". Errors: " . implode('; ', $errors);
        }

        toast_success($message);
        return redirect()->route('invoices.index');
    }

    /**
     * Mark invoice as paid
     */
    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            toast_error('Invoice is already marked as paid.');
            return redirect()->route('invoices.show', $invoice);
        }

        $invoice->update([
            'status' => 'paid',
            'paid_on' => now(),
        ]);

        toast_success('Invoice marked as paid successfully!');
        return redirect()->route('invoices.show', $invoice);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this month
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("%s-%s%s-%04d", $prefix, $year, $month, $newNumber);
    }
}
