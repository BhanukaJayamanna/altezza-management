<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.apartment', 'invoice.owner', 'recordedBy']);
        
        // If user is a owner, filter to only their payments
        if (Auth::user()->role === 'owner') {
            $ownerProfile = Auth::user()->owner;
            if ($ownerProfile) {
                $query->whereHas('invoice', function($q) use ($ownerProfile) {
                    $q->where('owner_id', $ownerProfile->id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Search by reference number or invoice number
        if ($request->filled('search')) {
            if (Auth::user()->role !== 'owner') {
                $query->where(function($q) use ($request) {
                    $q->where('reference_number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('invoice', function($invoiceQuery) use ($request) {
                          $invoiceQuery->where('invoice_number', 'like', '%' . $request->search . '%');
                      })
                      ->orWhereHas('invoice.owner', function($ownerQuery) use ($request) {
                          $ownerQuery->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            } else {
                $query->where('reference_number', 'like', '%' . $request->search . '%');
            }
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15)->withQueryString();
        
        // Get summary statistics
        $summaryQuery = Payment::query();
        if (Auth::user()->role === 'owner') {
            $ownerProfile = Auth::user()->owner;
            if ($ownerProfile) {
                $summaryQuery->whereHas('invoice', function($q) use ($ownerProfile) {
                    $q->where('owner_id', $ownerProfile->id);
                });
            } else {
                $summaryQuery->whereRaw('1 = 0');
            }
        }
        
        $totalAmount = $summaryQuery->sum('amount');
        $confirmedAmount = $summaryQuery->where('status', 'completed')->sum('amount');
        $pendingAmount = $summaryQuery->where('status', 'pending')->sum('amount');
        $monthlyAmount = $summaryQuery->whereMonth('payment_date', now()->month)->sum('amount');

        // Choose appropriate view based on user role
        $viewName = Auth::user()->role === 'owner' ? 'payments.owner-index' : 'payments.index';
        
        return view($viewName, compact(
            'payments', 'totalAmount', 'confirmedAmount', 'pendingAmount', 'monthlyAmount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Check if invoice_id is provided
        $invoiceId = $request->get('invoice_id');
        $invoice = null;
        
        if ($invoiceId) {
            $invoice = Invoice::with(['apartment', 'owner'])->findOrFail($invoiceId);
            
            // If user is owner, ensure they can only pay their own invoices
            if (Auth::user()->role === 'owner') {
                $ownerProfile = Auth::user()->owner;
                if (!$ownerProfile || $invoice->owner_id !== $ownerProfile->id) {
                    abort(403, 'Unauthorized access to this invoice.');
                }
            }
        } else if (Auth::user()->role === 'owner') {
            // For owners, show their unpaid invoices to select from
            $ownerProfile = Auth::user()->owner;
            if (!$ownerProfile) {
                abort(403, 'Owner profile not found.');
            }
            
            $unpaidInvoices = Invoice::where('owner_id', $ownerProfile->id)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->with('apartment')
                ->get();
                
            return view('payments.owner-create', compact('unpaidInvoices'));
        }
        
        // For admin/manager, they can create payments for any invoice
        $unpaidInvoices = Auth::user()->role !== 'owner' 
            ? Invoice::whereIn('status', ['pending', 'partial', 'overdue'])->with(['apartment', 'owner'])->get()
            : collect();
        
        return view('payments.create', compact('invoice', 'unpaidInvoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ];

        $request->validate($rules);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        // Check if user has permission to create payment for this invoice
        if (Auth::user()->role === 'owner') {
            $ownerProfile = Auth::user()->owner;
            if (!$ownerProfile || $invoice->owner_id !== $ownerProfile->id) {
                return back()->withErrors(['error' => 'You can only make payments for your own invoices.']);
            }
        }

        // Check remaining balance
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        $remainingBalance = $invoice->total_amount - $totalPaid;
        
        if ($request->amount > $remainingBalance) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining balance of $' . number_format($remainingBalance, 2)]);
        }

        // Create payment
        $payment = Payment::create([
            'invoice_id' => $request->invoice_id,
            'owner_id' => $invoice->owner_id,
            'amount' => $request->amount,
            'method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'status' => Auth::user()->role === 'owner' ? 'pending' : 'completed',
            'recorded_by' => Auth::id(),
        ]);

        // Update invoice status
        $this->updateInvoiceStatus($invoice);

        $message = Auth::user()->role === 'owner' 
            ? 'Payment submitted successfully and is pending approval.'
            : 'Payment recorded successfully.';

        toast_success($message);
        return redirect()->route('payments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        // Check permission
        if (Auth::user()->role === 'owner') {
            $ownerProfile = Auth::user()->owner;
            if (!$ownerProfile || $payment->invoice->owner_id !== $ownerProfile->id) {
                abort(403, 'Unauthorized access to this payment.');
            }
        }

        $payment->load(['invoice.apartment', 'invoice.owner', 'recordedBy']);
        
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        // Only admin/manager can edit payments
        if (Auth::user()->role === 'owner') {
            abort(403, 'Owners cannot edit payments.');
        }

        $payment->load(['invoice.apartment', 'invoice.owner']);
        
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        // Only admin/manager can update payments
        if (Auth::user()->role === 'owner') {
            abort(403, 'Owners cannot update payments.');
        }

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,completed,failed,cancelled'
        ];

        $request->validate($rules);

        // Check remaining balance (excluding this payment)
        $totalPaid = $payment->invoice->payments()
            ->where('id', '!=', $payment->id)
            ->where('status', 'completed')
            ->sum('amount');
        $remainingBalance = $payment->invoice->total_amount - $totalPaid;
        
        if ($request->amount > $remainingBalance) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining balance of $' . number_format($remainingBalance, 2)]);
        }

        $payment->update([
            'amount' => $request->amount,
            'method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        // Update invoice status
        $this->updateInvoiceStatus($payment->invoice);

        toast_success('Payment updated successfully!');
        return redirect()->route('payments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // Only admin/manager can delete payments
        if (Auth::user()->role === 'owner') {
            abort(403, 'Owners cannot delete payments.');
        }

        $invoice = $payment->invoice;
        $payment->delete();

        // Update invoice status after deletion
        $this->updateInvoiceStatus($invoice);

        toast_success('Payment deleted successfully!');
        return redirect()->route('payments.index');
    }

    /**
     * Approve a payment
     */
    public function approve(Payment $payment)
    {
        // Only admin/manager can approve payments
        if (Auth::user()->role === 'owner') {
            abort(403, 'Owners cannot approve payments.');
        }

        $payment->update(['status' => 'completed']);
        
        // Update invoice status
        $this->updateInvoiceStatus($payment->invoice);

        toast_success('Payment approved successfully!');
        return back();
    }

    /**
     * Reject a payment
     */
    public function reject(Payment $payment, Request $request)
    {
        // Only admin/manager can reject payments
        if (Auth::user()->role === 'owner') {
            abort(403, 'Owners cannot reject payments.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $payment->update([
            'status' => 'failed',
            'notes' => ($payment->notes ? $payment->notes . "\n\n" : '') . 
                      "Rejected: " . $request->rejection_reason
        ]);

        // Update invoice status
        $this->updateInvoiceStatus($payment->invoice);

        toast_success('Payment rejected successfully!');
        return back();
    }

    /**
     * Update invoice status based on payments
     */
    private function updateInvoiceStatus($invoice)
    {
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partial']);
        } else {
            // Check if overdue
            if ($invoice->due_date < now() && $invoice->status !== 'paid') {
                $invoice->update(['status' => 'overdue']);
            }
        }
    }
}
