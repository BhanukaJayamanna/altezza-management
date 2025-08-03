<?php

namespace App\Http\Controllers;

use App\Models\UtilityUnitPrice;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UtilityUnitPriceController extends Controller
{
    public function index(Request $request): View
    {
        $query = UtilityUnitPrice::query();

        // Filter by utility type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by effective date
        if ($request->filled('effective_from')) {
            $query->whereDate('effective_from', '>=', $request->effective_from);
        }

        // Sorting
        $sortBy = $request->get('sort', 'effective_from');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection)->orderBy('type');

        $unitPrices = $query->paginate(20);

        return view('utilities.unit-prices.index', compact('unitPrices'));
    }

    public function create(): View
    {
        return view('utilities.unit-prices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:electricity,water,gas',
            'price_per_unit' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Check for overlapping date ranges for the same utility type
        $overlapping = UtilityUnitPrice::where('type', $validated['type'])
            ->where('is_active', true)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    // New price starts during existing period
                    $q->where('effective_from', '<=', $validated['effective_from'])
                      ->where(function($subQ) use ($validated) {
                          $subQ->whereNull('effective_to')
                               ->orWhere('effective_to', '>=', $validated['effective_from']);
                      });
                })->orWhere(function($q) use ($validated) {
                    // New price ends during existing period
                    if ($validated['effective_to']) {
                        $q->where('effective_from', '<=', $validated['effective_to'])
                          ->where(function($subQ) use ($validated) {
                              $subQ->whereNull('effective_to')
                                   ->orWhere('effective_to', '>=', $validated['effective_to']);
                          });
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors([
                'effective_from' => 'The date range overlaps with an existing active price for this utility type.'
            ])->withInput();
        }

        UtilityUnitPrice::create($validated);

        toast_success('Utility unit price created successfully!');
        return redirect()->route('utility-unit-prices.index');
    }

    public function show(UtilityUnitPrice $utilityUnitPrice): View
    {
        $unitPrice = $utilityUnitPrice;
        return view('utilities.unit-prices.show', compact('unitPrice'));
    }

    public function edit(UtilityUnitPrice $utilityUnitPrice): View
    {
        $unitPrice = $utilityUnitPrice;
        return view('utilities.unit-prices.edit', compact('unitPrice'));
    }

    public function update(Request $request, UtilityUnitPrice $utilityUnitPrice): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:electricity,water,gas',
            'price_per_unit' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Check for overlapping date ranges (excluding current record)
        $overlapping = UtilityUnitPrice::where('type', $validated['type'])
            ->where('is_active', true)
            ->where('id', '!=', $utilityUnitPrice->id)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('effective_from', '<=', $validated['effective_from'])
                      ->where(function($subQ) use ($validated) {
                          $subQ->whereNull('effective_to')
                               ->orWhere('effective_to', '>=', $validated['effective_from']);
                      });
                })->orWhere(function($q) use ($validated) {
                    if ($validated['effective_to']) {
                        $q->where('effective_from', '<=', $validated['effective_to'])
                          ->where(function($subQ) use ($validated) {
                              $subQ->whereNull('effective_to')
                                   ->orWhere('effective_to', '>=', $validated['effective_to']);
                          });
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors([
                'effective_from' => 'The date range overlaps with an existing active price for this utility type.'
            ])->withInput();
        }

        $utilityUnitPrice->update($validated);

        toast_success('Utility unit price updated successfully!');
        return redirect()->route('utility-unit-prices.index');
    }

    public function destroy(UtilityUnitPrice $utilityUnitPrice): RedirectResponse
    {
        $utilityUnitPrice->delete();

        toast_success('Utility unit price deleted successfully!');
        return redirect()->route('utility-unit-prices.index');
    }

    public function current(): View
    {
        $currentPrices = [];
        
        foreach (['electricity', 'water', 'gas'] as $type) {
            $price = UtilityUnitPrice::getCurrentPrice($type);
            if ($price) {
                $currentPrices[$type] = UtilityUnitPrice::where('type', $type)
                    ->where('price_per_unit', $price)
                    ->where('is_active', true)
                    ->where('effective_from', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('effective_to')
                              ->orWhere('effective_to', '>=', now());
                    })
                    ->first();
            }
        }

        return view('utilities.unit-prices.current', compact('currentPrices'));
    }
}
