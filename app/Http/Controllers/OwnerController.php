<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Owner;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'owner');
        })->with(['ownerProfile', 'ownerProfile.apartment']);

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $owners = $query->paginate(15)->withQueryString();
        
        // Ensure all owner users have owner profiles
        foreach ($owners as $owner) {
            if (!$owner->ownerProfile && $owner->hasRole('owner')) {
                $owner->ownerProfile()->create([
                    'status' => 'active',
                ]);
            }
        }
        
        // Reload with owner profiles
        $owners->load(['ownerProfile', 'ownerProfile.apartment']);
        
        return view('owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableApartments = Apartment::where('status', 'vacant')->orderBy('number')->get();
        
        return view('owners.create', compact('availableApartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'status' => 'required|in:active,inactive,moved_out',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after:lease_start',
            'security_deposit' => 'nullable|numeric|min:0',
            'id_document' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Assign owner role
        $user->assignRole('owner');

        // Create owner profile
        $ownerData = [
            'user_id' => $user->id,
            'apartment_id' => $validated['apartment_id'] ?? null,
            'status' => $validated['status'],
            'lease_start' => $validated['lease_start'] ?? null,
            'lease_end' => $validated['lease_end'] ?? null,
            'id_document' => $validated['id_document'] ?? null,
            'emergency_contact' => $validated['emergency_contact_name'] ?? null,
            'emergency_phone' => $validated['emergency_contact_phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        Owner::create($ownerData);

        // Update apartment if selected
        if ($validated['apartment_id']) {
            $apartment = Apartment::find($validated['apartment_id']);
            $apartment->update([
                'owner_id' => $user->id,
                'status' => 'occupied'
            ]);
        }

        toast_success('Owner created successfully!');
        return redirect()->route('owners.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $owner)
    {
        $owner->load([
            'ownerProfile', 
            'ownerProfile.apartment',
            'ownerProfile.apartment.managementCorporation',
            'roles'
        ]);
        
        // Create owner profile if it doesn't exist for users with owner role
        if (!$owner->ownerProfile && $owner->hasRole('owner')) {
            $owner->ownerProfile()->create([
                'status' => 'active',
            ]);
            $owner->load('ownerProfile');
        }

        // Get additional related data if owner has an apartment
        $apartment = $owner->ownerProfile?->apartment;
        $recentInvoices = collect();
        $maintenanceRequests = collect();
        
        if ($apartment) {
            // Get recent invoices for the apartment (safely)
            try {
                $recentInvoices = $apartment->invoices()
                    ->latest()
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $recentInvoices = collect();
            }
                
            // Get recent maintenance requests (safely)
            try {
                $maintenanceRequests = $apartment->maintenanceRequests()
                    ->latest()
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $maintenanceRequests = collect();
            }
        }
        
        return view('owners.show', compact('owner', 'apartment', 'recentInvoices', 'maintenanceRequests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $owner)
    {
        $owner->load('ownerProfile');
        $availableApartments = Apartment::where('status', 'vacant')
            ->orWhere('owner_id', $owner->id)
            ->orderBy('number')
            ->get();
        
        return view('owners.edit', compact('owner', 'availableApartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $owner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $owner->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'status' => 'required|in:active,inactive,moved_out',
            'lease_start' => 'nullable|date',
            'lease_end' => 'nullable|date|after:lease_start',
            'security_deposit' => 'nullable|numeric|min:0',
            'id_document' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
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

        $owner->update($userData);

        // Update owner profile
        if ($owner->ownerProfile) {
            $owner->ownerProfile->update([
                'apartment_id' => $validated['apartment_id'] ?? null,
                'status' => $validated['status'],
                'lease_start' => $validated['lease_start'] ?? null,
                'lease_end' => $validated['lease_end'] ?? null,
                'id_document' => $validated['id_document'] ?? null,
                'emergency_contact' => $validated['emergency_contact_name'] ?? null,
                'emergency_phone' => $validated['emergency_contact_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        toast_success('Owner updated successfully!');
        return redirect()->route('owners.show', $owner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $owner)
    {
        // Remove owner from apartment if assigned
        if ($owner->ownerProfile && $owner->ownerProfile->apartment_id) {
            $apartment = Apartment::find($owner->ownerProfile->apartment_id);
            if ($apartment) {
                $apartment->update([
                    'owner_id' => null,
                    'status' => 'vacant'
                ]);
            }
        }

        // Delete owner profile and user
        if ($owner->ownerProfile) {
            $owner->ownerProfile->delete();
        }
        $owner->delete();

        toast_success('Owner deleted successfully!');
        return redirect()->route('owners.index');
    }
}
