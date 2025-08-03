<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use App\Models\Apartment;
use App\Notifications\PaymentVoucherStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage_vouchers')->except(['index', 'show']);
        $this->middleware('can:view_vouchers')->only(['index', 'show']);
    }

    /**
     * Display a listing of payment vouchers
     */
    public function index(Request $request)
    {
        $query = PaymentVoucher::with(['apartment', 'creator', 'approver'])
            ->latest('voucher_date');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_name')) {
            $query->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
        }

        if ($request->filled('expense_category')) {
            $query->where('expense_category', $request->expense_category);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }

        $vouchers = $query->paginate(15);

        // Get data for filters
        $apartments = Apartment::orderBy('number')->get();
        $categories = PaymentVoucher::distinct('expense_category')->pluck('expense_category');
        
        // Statistics
        $stats = [
            'total_vouchers' => PaymentVoucher::count(),
            'pending_vouchers' => PaymentVoucher::pending()->count(),
            'approved_vouchers' => PaymentVoucher::approved()->count(),
            'total_amount' => PaymentVoucher::approved()->sum('amount'),
            'pending_amount' => PaymentVoucher::pending()->sum('amount'),
        ];

        return view('vouchers.index', compact('vouchers', 'apartments', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new payment voucher
     */
    public function create()
    {
        $apartments = Apartment::orderBy('number')->get();
        $categories = [
            'maintenance' => 'Maintenance',
            'utility' => 'Utility',
            'supplies' => 'Supplies',
            'services' => 'Services',
            'cleaning' => 'Cleaning',
            'security' => 'Security',
            'landscaping' => 'Landscaping',
            'repairs' => 'Repairs',
            'general' => 'General',
        ];

        return view('vouchers.create', compact('apartments', 'categories'));
    }

    /**
     * Store a newly created payment voucher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_address' => 'nullable|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'apartment_id' => 'nullable|exists:apartments,id',
            'expense_category' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card',
            'reference_number' => 'nullable|string|max:255',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('voucher-receipts', 'public');
        }

        $validated['created_by'] = Auth::id();

        $voucher = PaymentVoucher::create($validated);

        return redirect()->route('vouchers.show', $voucher)
            ->with('success', 'Payment voucher created successfully. Voucher Number: ' . $voucher->voucher_number);
    }

    /**
     * Display the specified payment voucher
     */
    public function show(PaymentVoucher $voucher)
    {
        $voucher->load(['apartment', 'creator', 'approver']);
        return view('vouchers.show', compact('voucher'));
    }

    /**
     * Show the form for editing the specified payment voucher
     */
    public function edit(PaymentVoucher $voucher)
    {
        // Only allow editing pending vouchers
        if (!$voucher->isPending()) {
            return redirect()->route('vouchers.show', $voucher)
                ->with('error', 'Only pending vouchers can be edited.');
        }

        $apartments = Apartment::orderBy('number')->get();
        $categories = [
            'maintenance' => 'Maintenance',
            'utility' => 'Utility',
            'supplies' => 'Supplies',
            'services' => 'Services',
            'cleaning' => 'Cleaning',
            'security' => 'Security',
            'landscaping' => 'Landscaping',
            'repairs' => 'Repairs',
            'general' => 'General',
        ];

        return view('vouchers.edit', compact('voucher', 'apartments', 'categories'));
    }

    /**
     * Update the specified payment voucher
     */
    public function update(Request $request, PaymentVoucher $voucher)
    {
        // Only allow editing pending vouchers
        if (!$voucher->isPending()) {
            return redirect()->route('vouchers.show', $voucher)
                ->with('error', 'Only pending vouchers can be edited.');
        }

        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_address' => 'nullable|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'apartment_id' => 'nullable|exists:apartments,id',
            'expense_category' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card',
            'reference_number' => 'nullable|string|max:255',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt_file')) {
            // Delete old file if exists
            if ($voucher->receipt_file) {
                Storage::disk('public')->delete($voucher->receipt_file);
            }
            $validated['receipt_file'] = $request->file('receipt_file')->store('voucher-receipts', 'public');
        }

        $voucher->update($validated);

        return redirect()->route('vouchers.show', $voucher)
            ->with('success', 'Payment voucher updated successfully.');
    }

    /**
     * Approve a payment voucher
     */
    public function approve(Request $request, PaymentVoucher $voucher)
    {
        if (!$voucher->isPending()) {
            return redirect()->route('vouchers.show', $voucher)
                ->with('error', 'Only pending vouchers can be approved.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $voucher->approve(Auth::user(), $request->approval_notes);

        // Send notification to the creator
        if ($voucher->creator) {
            $voucher->creator->notify(new PaymentVoucherStatusChanged($voucher, 'approved'));
        }

        return redirect()->route('vouchers.show', $voucher)
            ->with('success', 'Payment voucher approved successfully.');
    }

    /**
     * Reject a payment voucher
     */
    public function reject(Request $request, PaymentVoucher $voucher)
    {
        if (!$voucher->isPending()) {
            return redirect()->route('vouchers.show', $voucher)
                ->with('error', 'Only pending vouchers can be rejected.');
        }

        $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $voucher->reject(Auth::user(), $request->approval_notes);

        // Send notification to the creator
        if ($voucher->creator) {
            $voucher->creator->notify(new PaymentVoucherStatusChanged($voucher, 'rejected'));
        }

        return redirect()->route('vouchers.show', $voucher)
            ->with('success', 'Payment voucher rejected.');
    }

    /**
     * Mark voucher as paid
     */
    public function markAsPaid(Request $request, PaymentVoucher $voucher)
    {
        if (!$voucher->isApproved()) {
            return redirect()->route('vouchers.show', $voucher)
                ->with('error', 'Only approved vouchers can be marked as paid.');
        }

        $request->validate([
            'payment_date' => 'required|date',
        ]);

        $voucher->markAsPaid($request->payment_date);

        return redirect()->route('vouchers.show', $voucher)
            ->with('success', 'Payment voucher marked as paid.');
    }

    /**
     * Export voucher as PDF
     */
    public function exportPDF(PaymentVoucher $voucher)
    {
        $voucher->load(['apartment', 'creator', 'approver']);
        
        $pdf = Pdf::loadView('vouchers.pdf', compact('voucher'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Voucher_' . $voucher->voucher_number . '.pdf');
    }

    /**
     * Delete the specified payment voucher
     */
    public function destroy(PaymentVoucher $voucher)
    {
        // Only allow deleting pending vouchers
        if (!$voucher->isPending()) {
            return redirect()->route('vouchers.index')
                ->with('error', 'Only pending vouchers can be deleted.');
        }

        // Delete associated file if exists
        if ($voucher->receipt_file) {
            Storage::disk('public')->delete($voucher->receipt_file);
        }

        $voucher->delete();

        return redirect()->route('vouchers.index')
            ->with('success', 'Payment voucher deleted successfully.');
    }
}
