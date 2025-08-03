<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lease::with(['apartment', 'tenant', 'owner']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by expiring leases
        if ($request->filled('expiring')) {
            $days = $request->expiring == 'soon' ? 30 : 7;
            $query->expiring($days);
        }

        // Search by lease number, tenant name, or apartment number
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('lease_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tenant', function($tenantQuery) use ($request) {
                      $tenantQuery->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('apartment', function($aptQuery) use ($request) {
                      $aptQuery->where('number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $leases = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();
        
        return view('leases.index', compact('leases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $apartments = Apartment::where('status', 'vacant')->orderBy('number')->get();
        $tenants = User::whereHas('roles', function($q) {
            $q->where('name', 'tenant');
        })->orderBy('name')->get();
        $owners = Owner::where('status', 'active')->orderBy('name')->get();
        
        // Pre-select tenant if passed via query parameter
        $selectedTenantId = $request->get('tenant_id');
        
        return view('leases.create', compact('apartments', 'tenants', 'owners', 'selectedTenantId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'tenant_id' => 'required|exists:users,id',
            'owner_id' => 'required|exists:owners,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'maintenance_charge' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|array',
            'terms_conditions.*' => 'nullable|string|max:500',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'status' => 'required|in:active,expired,terminated,renewed',
        ]);

        // Generate lease number
        $leaseNumber = 'LSE-' . date('Y') . '-' . str_pad(Lease::count() + 1, 4, '0', STR_PAD_LEFT);

        // Process terms_conditions - filter out empty values
        $termsConditions = [];
        if (isset($validated['terms_conditions']) && is_array($validated['terms_conditions'])) {
            $termsConditions = array_values(array_filter($validated['terms_conditions'], function($term) {
                return !empty(trim($term));
            }));
        }

        // Handle contract file upload
        $contractFilePath = null;
        if ($request->hasFile('contract_file')) {
            $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
        }

        $lease = Lease::create([
            'apartment_id' => $validated['apartment_id'],
            'tenant_id' => $validated['tenant_id'],
            'owner_id' => $validated['owner_id'],
            'lease_number' => $leaseNumber,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'rent_amount' => $validated['rent_amount'],
            'security_deposit' => $validated['security_deposit'] ?? 0,
            'maintenance_charge' => $validated['maintenance_charge'] ?? 0,
            'terms_conditions' => $termsConditions,
            'contract_file' => $contractFilePath,
            'status' => $validated['status'],
        ]);

        // Update apartment status if lease is active
        if ($lease->status === 'active') {
            $apartment = Apartment::find($validated['apartment_id']);
            $apartment->update([
                'status' => 'occupied',
                'tenant_id' => $validated['tenant_id'],
            ]);
            
            // Update tenant profile with lease information
            $tenant = User::find($validated['tenant_id']);
            if ($tenant && $tenant->tenantProfile) {
                $tenant->tenantProfile->update([
                    'apartment_id' => $validated['apartment_id'],
                    'lease_start' => $validated['start_date'],
                    'lease_end' => $validated['end_date'],
                ]);
            }
        }

        toast_success('Lease created successfully!');
        return redirect()->route('leases.show', $lease);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lease $lease)
    {
        $lease->load(['apartment', 'tenant', 'owner', 'invoices']);
        
        return view('leases.show', compact('lease'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lease $lease)
    {
        $apartments = Apartment::where(function($q) use ($lease) {
            $q->where('status', 'vacant')
              ->orWhere('id', $lease->apartment_id);
        })->orderBy('number')->get();
        
        $tenants = User::whereHas('roles', function($q) {
            $q->where('name', 'tenant');
        })->orderBy('name')->get();
        
        $owners = Owner::where('status', 'active')->orderBy('name')->get();
        
        return view('leases.edit', compact('lease', 'apartments', 'tenants', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'tenant_id' => 'required|exists:users,id',
            'owner_id' => 'required|exists:owners,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'maintenance_charge' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|array',
            'terms_conditions.*' => 'nullable|string|max:500',
            'status' => 'required|in:active,expired,terminated,renewed',
        ]);

        $oldApartmentId = $lease->apartment_id;
        $oldStatus = $lease->status;

        // Process terms_conditions - filter out empty values
        $termsConditions = [];
        if (isset($validated['terms_conditions']) && is_array($validated['terms_conditions'])) {
            $termsConditions = array_values(array_filter($validated['terms_conditions'], function($term) {
                return !empty(trim($term));
            }));
        }

        $lease->update([
            'apartment_id' => $validated['apartment_id'],
            'tenant_id' => $validated['tenant_id'],
            'owner_id' => $validated['owner_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'rent_amount' => $validated['rent_amount'],
            'security_deposit' => $validated['security_deposit'] ?? 0,
            'maintenance_charge' => $validated['maintenance_charge'] ?? 0,
            'terms_conditions' => $termsConditions,
            'status' => $validated['status'],
        ]);

        // Update apartment statuses
        if ($oldApartmentId !== $validated['apartment_id'] || $oldStatus !== $validated['status']) {
            // Clear old apartment
            if ($oldApartmentId && $oldStatus === 'active') {
                Apartment::find($oldApartmentId)->update([
                    'status' => 'vacant',
                    'tenant_id' => null,
                ]);
            }

            // Update new apartment
            if ($lease->status === 'active') {
                Apartment::find($validated['apartment_id'])->update([
                    'status' => 'occupied',
                    'tenant_id' => $validated['tenant_id'],
                ]);
                
                // Update tenant profile with lease information
                $tenant = User::find($validated['tenant_id']);
                if ($tenant && $tenant->tenantProfile) {
                    $tenant->tenantProfile->update([
                        'apartment_id' => $validated['apartment_id'],
                        'lease_start' => $validated['start_date'],
                        'lease_end' => $validated['end_date'],
                    ]);
                }
            }
        }

        toast_success('Lease updated successfully!');
        return redirect()->route('leases.show', $lease);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lease $lease)
    {
        // Check if lease has associated invoices
        if ($lease->invoices()->count() > 0) {
            toast_error('Cannot delete lease with associated invoices. Please remove invoices first.');
            return redirect()->route('leases.index');
        }

        // Update apartment status if lease was active
        if ($lease->status === 'active' && $lease->apartment) {
            $lease->apartment->update([
                'status' => 'vacant',
                'tenant_id' => null,
            ]);
        }

        $lease->delete();

        toast_success('Lease deleted successfully!');
        return redirect()->route('leases.index');
    }

    /**
     * Terminate a lease
     */
    public function terminate(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'termination_date' => 'required|date',
            'termination_reason' => 'nullable|string|max:500',
        ]);

        $lease->update([
            'status' => 'terminated',
            'end_date' => $validated['termination_date'],
        ]);

        // Update apartment status
        if ($lease->apartment) {
            $lease->apartment->update([
                'status' => 'vacant',
                'tenant_id' => null,
            ]);
        }

        toast_success('Lease terminated successfully!');
        return redirect()->route('leases.show', $lease);
    }

    /**
     * Renew a lease
     */
    public function renew(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'new_end_date' => 'required|date|after:' . $lease->end_date->format('Y-m-d'),
            'new_rent_amount' => 'nullable|numeric|min:0',
        ]);

        $lease->update([
            'end_date' => $validated['new_end_date'],
            'rent_amount' => $validated['new_rent_amount'] ?? $lease->rent_amount,
            'status' => 'renewed'
        ]);

        toast_success('Lease renewed successfully!');
        return redirect()->route('leases.show', $lease);
    }
}
