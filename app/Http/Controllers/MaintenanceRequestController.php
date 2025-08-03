<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenanceRequests = MaintenanceRequest::with(['apartment', 'tenant'])
            ->latest()
            ->paginate(15);

        return view('maintenance-requests.index', compact('maintenanceRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apartments = Apartment::with('tenant')->get();
        return view('maintenance-requests.create', compact('apartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string|max:100',
        ]);

        $validated['tenant_id'] = Auth::id();
        $validated['status'] = 'pending';

        MaintenanceRequest::create($validated);

        toast_success('Maintenance request created successfully!');
        return redirect()->route('maintenance-requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->load(['apartment', 'tenant']);
        return view('maintenance-requests.show', compact('maintenanceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        $apartments = Apartment::with('tenant')->get();
        return view('maintenance-requests.edit', compact('maintenanceRequest', 'apartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string|max:100',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $maintenanceRequest->update($validated);

        toast_success('Maintenance request updated successfully!');
        return redirect()->route('maintenance-requests.show', $maintenanceRequest);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->delete();

        toast_success('Maintenance request deleted successfully!');
        return redirect()->route('maintenance-requests.index');
    }

    /**
     * Tenant-specific methods
     */
    public function tenantIndex()
    {
        $user = Auth::user();
        $maintenanceRequests = $user->maintenanceRequests()
            ->with(['apartment'])
            ->latest()
            ->paginate(15);

        return view('tenant.maintenance-requests.index', compact('maintenanceRequests'));
    }

    public function tenantCreate()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant || !$tenant->apartment) {
            toast_error('You must be assigned to an apartment to create maintenance requests.');
            return redirect()->route('dashboard');
        }

        return view('tenant.maintenance-requests.create', compact('tenant'));
    }

    public function tenantStore(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant || !$tenant->apartment) {
            toast_error('You must be assigned to an apartment to create maintenance requests.');
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string|max:100',
        ]);

        $validated['apartment_id'] = $tenant->apartment_id;
        $validated['tenant_id'] = $user->id;
        $validated['status'] = 'pending';

        MaintenanceRequest::create($validated);

        toast_success('Maintenance request submitted successfully!');
        return redirect()->route('tenant.maintenance-requests');
    }

    public function tenantShow(MaintenanceRequest $maintenanceRequest)
    {
        // Ensure tenant can only view their own requests
        if ($maintenanceRequest->tenant_id !== Auth::id()) {
            abort(403);
        }

        $maintenanceRequest->load(['apartment']);
        return view('tenant.maintenance-requests.show', compact('maintenanceRequest'));
    }
}
