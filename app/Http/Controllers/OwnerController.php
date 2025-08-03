<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Owner::withCount(['apartments', 'apartments as occupied_apartments_count' => function($q) {
            $q->where('status', 'occupied');
        }]);

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $owners = $query->orderBy('name')->paginate(15)->withQueryString();
        
        return view('owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('owners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'id_document' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_routing' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        // Format bank details
        $bankDetails = null;
        if ($request->filled(['bank_account_name', 'bank_account_number', 'bank_name'])) {
            $bankDetails = [
                'account_name' => $request->bank_account_name,
                'account_number' => $request->bank_account_number,
                'bank_name' => $request->bank_name,
                'routing_number' => $request->bank_routing,
            ];
        }

        Owner::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'id_document' => $validated['id_document'],
            'bank_details' => $bankDetails,
            'status' => $validated['status'],
        ]);

        toast_success('Owner created successfully!');
        return redirect()->route('owners.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Owner $owner)
    {
        $owner->load(['apartments.tenant', 'leases.apartment', 'leases.tenant']);
        
        return view('owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'id_document' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_routing' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        // Format bank details
        $bankDetails = null;
        if ($request->filled(['bank_account_name', 'bank_account_number', 'bank_name'])) {
            $bankDetails = [
                'account_name' => $request->bank_account_name,
                'account_number' => $request->bank_account_number,
                'bank_name' => $request->bank_name,
                'routing_number' => $request->bank_routing,
            ];
        }

        $owner->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'id_document' => $validated['id_document'],
            'bank_details' => $bankDetails,
            'status' => $validated['status'],
        ]);

        toast_success('Owner updated successfully!');
        return redirect()->route('owners.show', $owner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Owner $owner)
    {
        // Check if owner has apartments
        if ($owner->apartments()->count() > 0) {
            toast_error('Cannot delete owner with associated apartments. Please reassign or remove apartments first.');
            return redirect()->route('owners.index');
        }

        $owner->delete();

        toast_success('Owner deleted successfully!');
        return redirect()->route('owners.index');
    }
}
