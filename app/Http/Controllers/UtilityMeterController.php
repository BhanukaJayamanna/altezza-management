<?php

namespace App\Http\Controllers;

use App\Models\UtilityMeter;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UtilityMeterController extends Controller
{
    public function index(): View
    {
        $meters = UtilityMeter::with(['apartment'])
            ->orderBy('apartment_id')
            ->orderBy('type')
            ->paginate(20);

        return view('utilities.meters.index', compact('meters'));
    }

    public function create(): View
    {
        $apartments = Apartment::orderBy('number')->get();
        
        return view('utilities.meters.create', compact('apartments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:electricity,water,gas',
            'meter_number' => 'required|string|unique:utility_meters',
            'last_reading' => 'nullable|numeric|min:0',
            'last_reading_date' => 'nullable|date',
            'rate_per_unit' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,faulty',
            'notes' => 'nullable|string'
        ]);

        // Check if meter type already exists for this apartment
        $existingMeter = UtilityMeter::where('apartment_id', $validated['apartment_id'])
            ->where('type', $validated['type'])
            ->first();

        if ($existingMeter) {
            return back()->withErrors([
                'type' => 'A ' . $validated['type'] . ' meter already exists for this apartment.'
            ])->withInput();
        }

        UtilityMeter::create($validated);

        toast_success('Utility meter created successfully!');
        return redirect()->route('utility-meters.index');
    }

    public function show(UtilityMeter $meter): View
    {
        $meter->load([
            'apartment.currentLease.owner', 
            'readings' => function($query) {
                $query->orderBy('reading_date', 'desc')->take(10);
            }
        ]);

        return view('utilities.meters.show', compact('meter'));
    }

    public function edit(UtilityMeter $meter): View
    {
        $apartments = Apartment::orderBy('number')->get();
        
        return view('utilities.meters.edit', compact('meter', 'apartments'));
    }

    public function update(Request $request, UtilityMeter $meter): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'type' => 'required|in:electricity,water,gas',
            'meter_number' => 'required|string|unique:utility_meters,meter_number,' . $meter->id,
            'last_reading' => 'nullable|numeric|min:0',
            'last_reading_date' => 'nullable|date',
            'rate_per_unit' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,faulty',
            'notes' => 'nullable|string'
        ]);

        // Check if meter type already exists for this apartment (excluding current meter)
        $existingMeter = UtilityMeter::where('apartment_id', $validated['apartment_id'])
            ->where('type', $validated['type'])
            ->where('id', '!=', $meter->id)
            ->first();

        if ($existingMeter) {
            return back()->withErrors([
                'type' => 'A ' . $validated['type'] . ' meter already exists for this apartment.'
            ])->withInput();
        }

        $meter->update($validated);

        toast_success('Utility meter updated successfully!');
        return redirect()->route('utility-meters.index');
    }

    public function destroy(UtilityMeter $meter): RedirectResponse
    {
        // Check if meter has readings
        if ($meter->readings()->count() > 0) {
            toast_error('Cannot delete meter that has readings.');
            return redirect()->route('utility-meters.index');
        }

        $meter->delete();

        toast_success('Utility meter deleted successfully!');
        return redirect()->route('utility-meters.index');
    }

    public function byApartment(Apartment $apartment): View
    {
        $meters = UtilityMeter::where('apartment_id', $apartment->id)
            ->with(['readings' => function($query) {
                $query->latest()->take(5);
            }])
            ->get();

        return view('utilities.meters.by-apartment', compact('apartment', 'meters'));
    }
}
