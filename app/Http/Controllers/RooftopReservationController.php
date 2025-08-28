<?php

namespace App\Http\Controllers;

use App\Models\RooftopReservation;
use App\Models\User;
use App\Models\Apartment;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class RooftopReservationController extends Controller
{
    /**
     * Display a listing of rooftop reservations.
     */
    public function index(Request $request): View
    {
        $query = RooftopReservation::with(['owner', 'apartment', 'invoice'])
                                  ->orderBy('reservation_date', 'desc')
                                  ->orderBy('start_time', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        if ($request->filled('owner')) {
            $query->whereHas('owner', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->owner . '%');
            });
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $reservations = $query->paginate(15)->withQueryString();

        $eventTypes = [
            'party' => 'Party',
            'wedding' => 'Wedding',
            'corporate' => 'Corporate Event',
            'family_gathering' => 'Family Gathering',
            'birthday' => 'Birthday Party',
            'other' => 'Other Event'
        ];

        return view('rooftop-reservations.index', compact('reservations', 'eventTypes'));
    }

    /**
     * Show the form for creating a new rooftop reservation.
     */
    public function create(): View
    {
        $owners = User::role('owner')->orderBy('name')->get();
        
        // Get apartments with their current owners
        $apartments = Apartment::with(['owner'])
                              ->where('status', 'occupied')
                              ->whereHas('owner')
                              ->orderBy('number')
                              ->get();

        // Get rooftop reservation settings
        $baseRate = Setting::getValue('rooftop_base_rate', 500.00);
        $hourlyRate = Setting::getValue('rooftop_hourly_rate', 100.00);
        $cleaningFee = Setting::getValue('rooftop_cleaning_fee', 150.00);
        $securityDeposit = Setting::getValue('rooftop_security_deposit', 1000.00);

        $eventTypes = [
            'party' => 'Party',
            'wedding' => 'Wedding',
            'corporate' => 'Corporate Event',
            'family_gathering' => 'Family Gathering',
            'birthday' => 'Birthday Party',
            'other' => 'Other Event'
        ];

        $equipmentOptions = [
            'tables_chairs' => 'Tables & Chairs',
            'sound_system' => 'Sound System',
            'lighting' => 'Additional Lighting',
            'decorations' => 'Decoration Setup',
            'catering_area' => 'Catering Setup Area',
            'stage_platform' => 'Stage/Platform'
        ];

        return view('rooftop-reservations.create', compact(
            'owners', 'apartments', 'eventTypes', 'equipmentOptions',
            'baseRate', 'hourlyRate', 'cleaningFee', 'securityDeposit'
        ));
    }

    /**
     * Store a newly created rooftop reservation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'reservation_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'event_type' => 'required|in:party,wedding,corporate,family_gathering,birthday,other',
            'event_title' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1|max:200',
            'special_requirements' => 'nullable|string',
            'terms_accepted' => 'required|accepted',
        ]);

        // Calculate duration in hours
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
        $durationHours = $endTime->diffInHours($startTime);

        // Check for overlapping reservations
        $overlapping = RooftopReservation::where('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })->exists();

        if ($overlapping) {
            return back()->withErrors(['time_conflict' => 'There is already a reservation during this time slot.'])->withInput();
        }

        // Get owner's apartment
        $owner = User::with(['apartment'])->findOrFail($validated['owner_id']);
        $apartmentId = $owner->apartment?->id;

        if (!$apartmentId) {
            return back()->withErrors(['owner_id' => 'Selected owner does not have an active apartment assignment.'])->withInput();
        }

        // Get pricing from settings
        $baseRatePerHour = (float) Setting::getValue('rooftop_base_rate_per_hour', 500);
        $guestChargePerPerson = (float) Setting::getValue('rooftop_guest_charge_per_person', 50);
        $securityDeposit = (float) Setting::getValue('rooftop_security_deposit', 2000);
        $cleaningCharge = (float) Setting::getValue('rooftop_cleaning_charge', 500);

        // Calculate amounts
        $baseAmount = $baseRatePerHour * $durationHours;
        $guestCharges = $guestChargePerPerson * $validated['number_of_guests'];
        $totalAmount = $baseAmount + $guestCharges + $securityDeposit + $cleaningCharge;

        DB::beginTransaction();

        try {
            $reservation = RooftopReservation::create([
                'reservation_number' => RooftopReservation::generateReservationNumber(),
                'owner_id' => $validated['owner_id'],
                'apartment_id' => $apartmentId,
                'reservation_date' => $validated['reservation_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_hours' => $durationHours,
                'event_type' => $validated['event_type'],
                'event_title' => $validated['event_title'],
                'number_of_guests' => $validated['number_of_guests'],
                'base_rate' => $baseRatePerHour,
                'base_amount' => $baseAmount,
                'guest_charges' => $guestCharges,
                'cleaning_charge' => $cleaningCharge,
                'security_deposit' => $securityDeposit,
                'total_amount' => $totalAmount,
                'special_requirements' => $validated['special_requirements'],
                'created_by' => Auth::id(),
                'status' => 'pending'
            ]);

            DB::commit();

            toast_success("Rooftop reservation created successfully and is pending approval!");
            return redirect()->route('rooftop-reservations.show', $reservation);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating rooftop reservation: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create reservation. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified rooftop reservation.
     */
    public function show(RooftopReservation $rooftopReservation): View
    {
        $rooftopReservation->load(['owner', 'apartment', 'invoice', 'payments', 'createdBy', 'approvedBy']);
        
        return view('rooftop-reservations.show', compact('rooftopReservation'));
    }

    /**
     * Show the form for editing the specified rooftop reservation.
     */
    public function edit(RooftopReservation $rooftopReservation): View
    {
        // Only allow editing if reservation is pending or confirmed and not in the past
        if (!in_array($rooftopReservation->status, ['pending', 'confirmed']) || 
            $rooftopReservation->reservation_date->isPast()) {
            abort(403, 'This reservation cannot be edited.');
        }

        $owners = User::role('owner')->orderBy('name')->get();
        
        // Get apartments with their current owners
        $apartments = Apartment::with(['owner'])
                              ->where('status', 'occupied')
                              ->whereHas('owner')
                              ->orderBy('number')
                              ->get();

        $eventTypes = [
            'party' => 'Party',
            'wedding' => 'Wedding',
            'corporate' => 'Corporate Event',
            'family_gathering' => 'Family Gathering',
            'birthday' => 'Birthday Party',
            'other' => 'Other Event'
        ];

        $equipmentOptions = [
            'tables_chairs' => 'Tables & Chairs',
            'sound_system' => 'Sound System',
            'lighting' => 'Additional Lighting',
            'decorations' => 'Decoration Setup',
            'catering_area' => 'Catering Setup Area',
            'stage_platform' => 'Stage/Platform'
        ];

        return view('rooftop-reservations.edit', compact(
            'rooftopReservation', 'owners', 'apartments', 'eventTypes', 'equipmentOptions'
        ));
    }

    /**
     * Update the specified rooftop reservation.
     */
    public function update(Request $request, RooftopReservation $rooftopReservation): RedirectResponse
    {
        // Only allow updates if reservation is pending or confirmed and not in the past
        if (!in_array($rooftopReservation->status, ['pending', 'confirmed']) || 
            $rooftopReservation->reservation_date->isPast()) {
            toast_error('This reservation cannot be updated.');
            return back();
        }

        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'apartment_id' => 'required|exists:apartments,id',
            'reservation_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'event_type' => 'required|in:party,wedding,corporate,family_gathering,birthday,other',
            'event_title' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'expected_guests' => 'required|integer|min:1|max:200',
            'base_rate' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'cleaning_fee' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'special_requirements' => 'nullable|string',
            'equipment_requested' => 'nullable|array',
            'catering_allowed' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        // Calculate duration
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
        $durationHours = $endTime->diffInHours($startTime);

        // Check for overlapping reservations (excluding current reservation)
        $overlapping = RooftopReservation::overlapping(
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time'],
            $rooftopReservation->id
        )->exists();

        if ($overlapping) {
            return back()->withErrors(['time_conflict' => 'There is already a reservation during this time slot.'])->withInput();
        }

        DB::beginTransaction();

        try {
            // Calculate total amount
            $subtotal = ($validated['base_rate'] + ($validated['hourly_rate'] * $durationHours)) + 
                       $validated['cleaning_fee'] + $validated['security_deposit'] + 
                       ($validated['additional_charges'] ?? 0);
            $totalAmount = $subtotal - ($validated['discount'] ?? 0);

            // Update payment due dates if reservation date changed
            $depositDueDate = $rooftopReservation->deposit_due_date;
            $finalPaymentDueDate = $rooftopReservation->final_payment_due_date;
            
            if ($rooftopReservation->reservation_date != $validated['reservation_date']) {
                $depositDueDate = Carbon::parse($validated['reservation_date'])->subWeeks(2);
                $finalPaymentDueDate = Carbon::parse($validated['reservation_date'])->subDays(3);
            }

            $rooftopReservation->update([
                'owner_id' => $validated['owner_id'],
                'apartment_id' => $validated['apartment_id'],
                'reservation_date' => $validated['reservation_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_hours' => $durationHours,
                'event_type' => $validated['event_type'],
                'event_title' => $validated['event_title'],
                'event_description' => $validated['event_description'],
                'expected_guests' => $validated['expected_guests'],
                'base_rate' => $validated['base_rate'],
                'hourly_rate' => $validated['hourly_rate'],
                'cleaning_fee' => $validated['cleaning_fee'],
                'security_deposit' => $validated['security_deposit'],
                'additional_charges' => $validated['additional_charges'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'total_amount' => $totalAmount,
                'deposit_due_date' => $depositDueDate,
                'final_payment_due_date' => $finalPaymentDueDate,
                'special_requirements' => $validated['special_requirements'],
                'equipment_requested' => $validated['equipment_requested'] ?? [],
                'catering_allowed' => $validated['catering_allowed'] ?? [],
                'notes' => $validated['notes'],
            ]);

            // Update related invoice if total amount changed
            if ($rooftopReservation->invoice && $rooftopReservation->isDirty('total_amount')) {
                $rooftopReservation->invoice->update([
                    'total_amount' => $totalAmount,
                    'amount' => $totalAmount
                ]);
            }

            DB::commit();

            toast_success("Rooftop reservation {$rooftopReservation->reservation_number} updated successfully!");
            return redirect()->route('rooftop-reservations.show', $rooftopReservation);

        } catch (\Exception $e) {
            DB::rollback();
            toast_error('Failed to update reservation: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified rooftop reservation.
     */
    public function destroy(RooftopReservation $rooftopReservation): RedirectResponse
    {
        // Only allow deletion if no payments have been made
        if ($rooftopReservation->payments()->where('status', 'confirmed')->exists()) {
            toast_error('Cannot delete reservation with confirmed payments.');
            return back();
        }

        DB::beginTransaction();

        try {
            // Delete related invoice if exists and has no payments
            if ($rooftopReservation->invoice && 
                !$rooftopReservation->invoice->payments()->where('status', 'confirmed')->exists()) {
                $rooftopReservation->invoice->delete();
            }

            $reservationNumber = $rooftopReservation->reservation_number;
            $rooftopReservation->delete();

            DB::commit();

            toast_success("Rooftop reservation {$reservationNumber} deleted successfully!");
            return redirect()->route('rooftop-reservations.index');

        } catch (\Exception $e) {
            DB::rollback();
            toast_error('Failed to delete reservation: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Approve a pending rooftop reservation.
     */
    public function approve(RooftopReservation $rooftopReservation): RedirectResponse
    {
        if ($rooftopReservation->status !== 'pending') {
            toast_error('Only pending reservations can be approved.');
            return back();
        }

        DB::beginTransaction();

        try {
            // Update reservation status
            $rooftopReservation->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Generate invoice if not already generated
            if (!$rooftopReservation->invoice_id) {
                $invoice = $this->createInvoiceFromReservation($rooftopReservation);
                $rooftopReservation->update(['invoice_id' => $invoice->id]);
            }

            DB::commit();

            toast_success("Rooftop reservation {$rooftopReservation->reservation_number} approved successfully! Invoice has been generated.");
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving rooftop reservation: ' . $e->getMessage());
            
            toast_error('Failed to approve reservation. Please try again.');
            return back();
        }
    }

    /**
     * Cancel a rooftop reservation.
     */
    public function cancel(Request $request, RooftopReservation $rooftopReservation): RedirectResponse
    {
        if (!$rooftopReservation->canBeCancelled()) {
            toast_error('This reservation cannot be cancelled.');
            return back();
        }

        $rooftopReservation->update([
            'status' => 'cancelled',
            'admin_notes' => ($rooftopReservation->admin_notes ?? '') . "\n" . 
                           "Cancelled on " . now()->format('Y-m-d H:i:s') . 
                           " by " . Auth::user()->name . 
                           ". Reason: " . ($request->input('reason', 'No reason provided'))
        ]);

        toast_success("Rooftop reservation {$rooftopReservation->reservation_number} cancelled successfully!");
        return back();
    }

    /**
     * Mark a rooftop reservation as completed.
     */
    public function complete(RooftopReservation $rooftopReservation): RedirectResponse
    {
        if ($rooftopReservation->status !== 'approved') {
            toast_error('Only approved reservations can be marked as completed.');
            return back();
        }

        if ($rooftopReservation->reservation_date->isFuture()) {
            toast_error('Cannot mark future reservations as completed.');
            return back();
        }

        $rooftopReservation->update([
            'status' => 'completed'
        ]);

        toast_success("Rooftop reservation {$rooftopReservation->reservation_number} marked as completed!");
        return back();
    }

    /**
     * Generate PDF for rooftop reservation.
     */
    public function downloadPdf(RooftopReservation $rooftopReservation)
    {
        $rooftopReservation->load(['owner', 'apartment', 'invoice', 'createdBy', 'approvedBy']);
        
        $pdf = Pdf::loadView('rooftop-reservations.pdf', compact('rooftopReservation'));
        
        $filename = 'rooftop_reservation_' . $rooftopReservation->reservation_number . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Get available time slots for a specific date.
     */
    public function getAvailableTimeSlots(Request $request)
    {
        $date = $request->input('date');
        $excludeId = $request->input('exclude_id');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $availableSlots = RooftopReservation::getAvailableTimeSlots($date, $excludeId);
        
        return response()->json($availableSlots);
    }

    /**
     * Create invoice from rooftop reservation.
     */
    private function createInvoiceFromReservation(RooftopReservation $reservation): Invoice
    {
        // Generate unique invoice number
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);
        
        // Create line items for detailed breakdown
        $lineItems = [
            [
                'description' => 'Rooftop Base Amount (' . $reservation->duration_hours . ' hours)',
                'quantity' => $reservation->duration_hours,
                'unit' => 'hour',
                'rate' => $reservation->base_rate,
                'amount' => $reservation->base_amount
            ],
            [
                'description' => 'Guest Charges (' . $reservation->number_of_guests . ' guests)',
                'quantity' => $reservation->number_of_guests,
                'unit' => 'person',
                'rate' => $reservation->guest_charges / $reservation->number_of_guests,
                'amount' => $reservation->guest_charges
            ],
            [
                'description' => 'Cleaning Charge',
                'quantity' => 1,
                'unit' => 'service',
                'rate' => $reservation->cleaning_charge,
                'amount' => $reservation->cleaning_charge
            ],
            [
                'description' => 'Security Deposit',
                'quantity' => 1,
                'unit' => 'deposit',
                'rate' => $reservation->security_deposit,
                'amount' => $reservation->security_deposit
            ]
        ];

        if ($reservation->additional_charges > 0) {
            $lineItems[] = [
                'description' => 'Additional Charges',
                'quantity' => 1,
                'unit' => 'service',
                'rate' => $reservation->additional_charges,
                'amount' => $reservation->additional_charges
            ];
        }

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'type' => 'rooftop_reservation',
            'apartment_id' => $reservation->apartment_id,
            'owner_id' => $reservation->owner_id,
            'rooftop_reservation_id' => $reservation->id,
            'billing_period_start' => $reservation->reservation_date,
            'billing_period_end' => $reservation->reservation_date,
            'amount' => $reservation->total_amount,
            'late_fee' => 0,
            'discount' => $reservation->discount,
            'total_amount' => $reservation->total_amount,
            'due_date' => $reservation->final_payment_due_date,
            'status' => 'pending',
            'description' => 'Rooftop reservation for ' . $reservation->event_title . ' on ' . $reservation->reservation_date->format('F j, Y'),
            'line_items' => $lineItems,
            'created_by' => Auth::id()
        ]);
    }
}
