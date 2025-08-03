<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Apartment::with(['owner', 'tenant']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by block
        if ($request->filled('block')) {
            $query->where('block', $request->block);
        }

        // Search by apartment number
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        $apartments = $query->paginate(12)->withQueryString();
        
        // Get unique values for filters
        $statuses = Apartment::distinct()->pluck('status')->filter();
        $types = Apartment::distinct()->pluck('type')->filter();
        $blocks = Apartment::distinct()->pluck('block')->filter();

        return view('apartments.index', compact('apartments', 'statuses', 'types', 'blocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owners = Owner::orderBy('name')->get();
        $tenants = User::whereHas('roles', function($query) {
            $query->where('name', 'tenant');
        })->orderBy('name')->get();
        
        $typeOptions = ['1bhk', '2bhk', '3bhk', '4bhk', 'studio', 'penthouse'];
        $statusOptions = ['vacant', 'occupied', 'maintenance'];
        
        return view('apartments.create', compact('owners', 'tenants', 'typeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:apartments,number',
            'type' => 'required|in:1bhk,2bhk,3bhk,4bhk,studio,penthouse',
            'block' => 'nullable|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:1',
            'status' => 'required|in:vacant,occupied,maintenance',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:owners,id',
            'tenant_id' => 'nullable|exists:users,id',
            'rent_amount' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
        ]);

        Apartment::create($validated);

        toast_success('Apartment created successfully!');
        return redirect()->route('apartments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        $apartment->load(['owner', 'tenant', 'tenantProfile', 'invoices' => function($query) {
            $query->latest()->limit(5);
        }, 'maintenanceRequests' => function($query) {
            $query->latest()->limit(5);
        }]);
        
        return view('apartments.show', compact('apartment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        $owners = Owner::orderBy('name')->get();
        $tenants = User::whereHas('roles', function($query) {
            $query->where('name', 'tenant');
        })->orderBy('name')->get();
        
        $typeOptions = ['1bhk', '2bhk', '3bhk', '4bhk', 'studio', 'penthouse'];
        $statusOptions = ['vacant', 'occupied', 'maintenance'];
        
        return view('apartments.edit', compact('apartment', 'owners', 'tenants', 'typeOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:apartments,number,' . $apartment->id,
            'type' => 'required|in:1bhk,2bhk,3bhk,4bhk,studio,penthouse',
            'block' => 'nullable|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:1',
            'status' => 'required|in:vacant,occupied,maintenance',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:owners,id',
            'tenant_id' => 'nullable|exists:users,id',
            'rent_amount' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
        ]);

        $apartment->update($validated);
        toast_success('Apartment updated successfully!');
        return redirect()->route('apartments.show', $apartment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        $apartment->delete();

        toast_success('Apartment deleted successfully!');
        return redirect()->route('apartments.index');
    }
}
