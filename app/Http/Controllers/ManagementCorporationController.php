<?php

namespace App\Http\Controllers;

use App\Models\ManagementCorporation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagementCorporationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ManagementCorporation::withCount(['apartments', 'apartments as occupied_apartments_count' => function($q) {
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

        $managementCorporations = $query->orderBy('name')->paginate(15)->withQueryString();
        
        return view('management-corporations.index', compact('managementCorporations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('management-corporations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:management_corporations,email',
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

        ManagementCorporation::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'id_document' => $validated['id_document'],
            'bank_details' => $bankDetails,
            'status' => $validated['status'],
        ]);

        toast_success('Management Corporation created successfully!');
        return redirect()->route('management-corporations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ManagementCorporation $managementCorporation)
    {
        $managementCorporation->load(['apartments.owner']);
        
        return view('management-corporations.show', compact('managementCorporation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManagementCorporation $managementCorporation)
    {
        return view('management-corporations.edit', compact('managementCorporation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManagementCorporation $managementCorporation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:management_corporations,email,' . $managementCorporation->id,
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

        $managementCorporation->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'id_document' => $validated['id_document'],
            'bank_details' => $bankDetails,
            'status' => $validated['status'],
        ]);

        toast_success('Management Corporation updated successfully!');
        return redirect()->route('management-corporations.show', $managementCorporation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManagementCorporation $managementCorporation)
    {
        // Check if management corporation has apartments
        if ($managementCorporation->apartments()->count() > 0) {
            toast_error('Cannot delete management corporation with associated apartments. Please reassign or remove apartments first.');
            return redirect()->route('management-corporations.index');
        }

        $managementCorporation->delete();

        toast_success('Management Corporation deleted successfully!');
        return redirect()->route('management-corporations.index');
    }
}
