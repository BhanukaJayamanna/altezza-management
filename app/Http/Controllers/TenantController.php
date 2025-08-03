<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'tenant');
        })->with(['tenantProfile', 'tenantProfile.apartment'])
          ->withCount(['leases']);

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $tenants = $query->paginate(15)->withQueryString();
        
        // Ensure all tenant users have tenant profiles
        foreach ($tenants as $tenant) {
            if (!$tenant->tenantProfile && $tenant->hasRole('tenant')) {
                $tenant->tenantProfile()->create([
                    'status' => 'active',
                ]);
            }
        }
        
        // Reload with tenant profiles
        $tenants->load(['tenantProfile', 'tenantProfile.apartment']);
        
        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableApartments = Apartment::where('status', 'vacant')->orderBy('number')->get();
        
        return view('tenants.create', compact('availableApartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after:lease_start',
            'security_deposit' => 'nullable|numeric|min:0',
            'id_document' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Assign tenant role
        $user->assignRole('tenant');

        // Create tenant profile
        $tenantData = [
            'user_id' => $user->id,
            'apartment_id' => $validated['apartment_id'] ?? null,
            'lease_start' => $validated['lease_start'] ?? null,
            'lease_end' => $validated['lease_end'] ?? null,
            'id_document' => $validated['id_document'] ?? null,
            'emergency_contact' => $validated['emergency_contact_name'] ?? null,
            'emergency_phone' => $validated['emergency_contact_phone'] ?? null,
            'status' => 'active',
        ];

        Tenant::create($tenantData);

        // Update apartment if selected
        if ($validated['apartment_id']) {
            $apartment = Apartment::find($validated['apartment_id']);
            $apartment->update([
                'tenant_id' => $user->id,
                'status' => 'occupied'
            ]);
        }

        toast_success('Tenant created successfully!');
        return redirect()->route('tenants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $tenant)
    {
        $tenant->load(['tenantProfile', 'tenantProfile.apartment', 'leases' => function($query) {
            $query->with(['apartment', 'owner'])->orderBy('start_date', 'desc');
        }]);
        
        // Create tenant profile if it doesn't exist for users with tenant role
        if (!$tenant->tenantProfile && $tenant->hasRole('tenant')) {
            $tenant->tenantProfile()->create([
                'status' => 'active',
            ]);
            $tenant->load('tenantProfile');
        }
        
        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $tenant)
    {
        $tenant->load('tenantProfile');
        $availableApartments = Apartment::where('status', 'vacant')
            ->orWhere('tenant_id', $tenant->id)
            ->orderBy('number')
            ->get();
        
        return view('tenants.edit', compact('tenant', 'availableApartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $tenant->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after:lease_start',
            'security_deposit' => 'nullable|numeric|min:0',
            'id_document' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        // Update user
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if ($validated['password']) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $tenant->update($userData);

        // Update tenant profile
        if ($tenant->tenantProfile) {
            $tenant->tenantProfile->update([
                'apartment_id' => $validated['apartment_id'] ?? null,
                'lease_start' => $validated['lease_start'] ?? null,
                'lease_end' => $validated['lease_end'] ?? null,
                'id_document' => $validated['id_document'] ?? null,
                'emergency_contact' => $validated['emergency_contact_name'] ?? null,
                'emergency_phone' => $validated['emergency_contact_phone'] ?? null,
            ]);
        }

        toast_success('Tenant updated successfully!');
        return redirect()->route('tenants.show', $tenant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $tenant)
    {
        // Remove tenant from apartment if assigned
        if ($tenant->tenantProfile && $tenant->tenantProfile->apartment_id) {
            $apartment = Apartment::find($tenant->tenantProfile->apartment_id);
            if ($apartment) {
                $apartment->update([
                    'tenant_id' => null,
                    'status' => 'vacant'
                ]);
            }
        }

        // Delete tenant profile and user
        if ($tenant->tenantProfile) {
            $tenant->tenantProfile->delete();
        }
        $tenant->delete();

        toast_success('Tenant deleted successfully!');
        return redirect()->route('tenants.index');
    }
}
