<?php

namespace App\Http\Controllers;

use App\Models\UtilityReading;
use App\Models\UtilityMeter;
use App\Models\UtilityUnitPrice;
use App\Services\UtilityBillService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UtilityReadingController extends Controller
{
    protected $utilityBillService;

    public function __construct(UtilityBillService $utilityBillService)
    {
        $this->utilityBillService = $utilityBillService;
    }

    public function index(Request $request): View
    {
        $query = UtilityReading::with(['meter.apartment', 'recordedBy']);

        // Filter by meter
        if ($request->filled('meter_id')) {
            $query->where('meter_id', $request->meter_id);
        }

        // Filter by utility type if provided
        if ($request->filled('type')) {
            $query->whereHas('meter', function($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('reading_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort', 'reading_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $readings = $query->paginate(20);

        // Get filter options
        $meters = UtilityMeter::with('apartment')->orderBy('apartment_id')->orderBy('type')->get();

        return view('utilities.readings.index', compact('readings', 'meters'));
    }

    public function create(): View
    {
        $meters = UtilityMeter::where('status', 'active')
            ->with('apartment')
            ->orderBy('apartment_id')
            ->orderBy('type')
            ->get();

        return view('utilities.readings.create', compact('meters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meter_id' => 'required|exists:utility_meters,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date|before_or_equal:today',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after:billing_period_start',
            'notes' => 'nullable|string'
        ]);

        $meter = UtilityMeter::find($validated['meter_id']);
        
        // Check if current reading is less than previous reading
        $lastReading = $meter->getLatestReading();
        if ($lastReading && $validated['current_reading'] < $lastReading->current_reading) {
            return back()->withErrors([
                'current_reading' => 'Current reading cannot be less than the previous reading (' . $lastReading->current_reading . ').'
            ])->withInput();
        }

        // Calculate previous reading and consumption
        $validated['previous_reading'] = $lastReading ? $lastReading->current_reading : 0;
        $validated['consumption'] = $validated['current_reading'] - $validated['previous_reading'];
        $validated['recorded_by'] = Auth::id();

        // Calculate amount before creating the record
        $unitPrice = UtilityUnitPrice::getCurrentPrice($meter->type, Carbon::parse($validated['reading_date']));
        $validated['amount'] = $validated['consumption'] * ($unitPrice ?? $meter->rate_per_unit);

        $reading = UtilityReading::create($validated);

        // Update meter's last reading
        $meter->update([
            'last_reading' => $validated['current_reading'],
            'last_reading_date' => $validated['reading_date']
        ]);

        toast_success('Utility reading recorded successfully!');
        return redirect()->route('utility-readings.index');
    }

    public function show(UtilityReading $reading): View
    {
        $reading->load(['meter.apartment', 'recordedBy']);

        return view('utilities.readings.show', compact('reading'));
    }

    public function edit(UtilityReading $reading): View
    {
        $meters = UtilityMeter::where('status', 'active')
            ->with('apartment')
            ->orderBy('apartment_id')
            ->orderBy('type')
            ->get();

        return view('utilities.readings.edit', compact('reading', 'meters'));
    }

    public function update(Request $request, UtilityReading $reading): RedirectResponse
    {
        $validated = $request->validate([
            'meter_id' => 'required|exists:utility_meters,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date|before_or_equal:today',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after:billing_period_start',
            'notes' => 'nullable|string'
        ]);

        // Recalculate consumption
        $validated['consumption'] = $validated['current_reading'] - $reading->previous_reading;

        $reading->update($validated);

        // Recalculate amount
        $this->utilityBillService->calculateReadingAmount($reading);

        toast_success('Utility reading updated successfully!');
        return redirect()->route('utility-readings.index');
    }

    public function destroy(UtilityReading $reading): RedirectResponse
    {
        // Check if reading has associated bills
        if ($reading->bill) {
            toast_error('Cannot delete reading that has associated bills.');
            return redirect()->route('utility-readings.index');
        }

        $reading->delete();

        toast_success('Utility reading deleted successfully!');
        return redirect()->route('utility-readings.index');
    }

    public function bulkEntry(): View
    {
        $meters = UtilityMeter::where('status', 'active')
            ->with(['apartment', 'readings' => function($query) {
                $query->latest()->take(1);
            }])
            ->orderBy('apartment_id')
            ->orderBy('type')
            ->get();

        // Get unique blocks for filtering
        $blocks = $meters->pluck('apartment.block')->unique()->sort()->values();

        return view('utilities.readings.bulk', compact('meters', 'blocks'));
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reading_date' => 'required|date|before_or_equal:today',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after:billing_period_start',
            'readings' => 'required|array',
            'readings.*.meter_id' => 'required|exists:utility_meters,id',
            'readings.*.current_reading' => 'required|numeric|min:0'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($validated['readings'] as $readingData) {
            if (empty($readingData['current_reading'])) {
                continue; // Skip empty readings
            }

            $meter = UtilityMeter::find($readingData['meter_id']);
            $lastReading = $meter->getLatestReading();

            // Validate current reading
            if ($lastReading && $readingData['current_reading'] < $lastReading->current_reading) {
                $errors[] = "Meter {$meter->meter_number}: Current reading cannot be less than previous reading.";
                continue;
            }

            try {
                $consumption = $readingData['current_reading'] - ($lastReading ? $lastReading->current_reading : 0);
                
                // Calculate amount before creating the record
                $unitPrice = UtilityUnitPrice::getCurrentPrice($meter->type, Carbon::parse($validated['reading_date']));
                $amount = $consumption * ($unitPrice ?? $meter->rate_per_unit);

                $reading = UtilityReading::create([
                    'meter_id' => $readingData['meter_id'],
                    'current_reading' => $readingData['current_reading'],
                    'previous_reading' => $lastReading ? $lastReading->current_reading : 0,
                    'consumption' => $consumption,
                    'amount' => $amount,
                    'reading_date' => $validated['reading_date'],
                    'billing_period_start' => $validated['billing_period_start'],
                    'billing_period_end' => $validated['billing_period_end'],
                    'recorded_by' => Auth::id()
                ]);

                // Update meter's last reading
                $meter->update([
                    'last_reading' => $readingData['current_reading'],
                    'last_reading_date' => $validated['reading_date']
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Meter {$meter->meter_number}: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            toast_warning("Recorded {$successCount} readings with some errors: " . implode(', ', $errors));
            return redirect()->route('utility-readings.bulk');
        }

        toast_success("Successfully recorded {$successCount} utility readings!");
        return redirect()->route('utility-readings.index');
    }

    public function generateBills(Request $request): RedirectResponse
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
            return back();
        }

        toast_success("Successfully generated {$results['generated']} utility bills for {$validated['month']}/{$validated['year']}!");
        return back();
    }
}
