<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ManagementCorporation;
use App\Models\User;
use App\Models\Setting;
use App\Services\ManagementFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartmentController extends Controller
{
    protected $managementFeeService;

    public function __construct(ManagementFeeService $managementFeeService)
    {
        $this->managementFeeService = $managementFeeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Apartment::with(['managementCorporation', 'currentOwner']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by assessment_no
        if ($request->filled('assessment_no')) {
            $query->where('assessment_no', $request->assessment_no);
        }

        // Search by apartment number
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        $apartments = $query->paginate(12)->withQueryString();
        
        // Get unique values for filters
        $statuses = Apartment::distinct()->pluck('status')->filter();
        $types = Apartment::distinct()->pluck('type')->filter();
        $assessment_nos = Apartment::distinct()->pluck('assessment_no')->filter();

        return view('apartments.index', compact('apartments', 'statuses', 'types', 'assessment_nos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managementCorporations = ManagementCorporation::orderBy('name')->get();
        // Get actual owners from owners table with their user information
        $owners = \App\Models\Owner::with('user')->get();
        
        $typeOptions = ['1bhk', '2bhk', '3bhk', '4bhk', 'studio', 'penthouse'];
        $statusOptions = ['vacant', 'occupied', 'maintenance'];
        
        // Get current management fee ratios for display
        $managementFeeSettings = $this->managementFeeService->getCurrentSettings();
        
        return view('apartments.create', compact(
            'managementCorporations', 
            'owners', 
            'typeOptions', 
            'statusOptions',
            'managementFeeSettings'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:apartments,number',
            'type' => 'required|in:1bhk,2bhk,3bhk,4bhk,studio,penthouse',
            'assessment_no' => 'nullable|string|max:50',
            'area' => 'required|numeric|min:1', // Made required for management fee calculation
            'status' => 'required|in:vacant,occupied,maintenance',
            'description' => 'nullable|string',
            'management_corporation_id' => 'required|exists:management_corporations,id', // Required - apartments must have management corp
            'owner_id' => 'nullable|exists:owners,id', // Optional - apartments can exist without owners
            'rent_amount' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            // Management fee settings (optional override)
            'management_fee_ratio' => 'nullable|numeric|min:0|max:999.99',
            'sinking_fund_ratio' => 'nullable|numeric|min:0|max:999.99',
            'create_management_fee' => 'boolean',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Check if owner assignment will auto-update status
                $willAutoUpdate = $validated['owner_id'] && $validated['status'] === 'vacant';

                // Create the apartment (model events will handle status and relationship sync)
                $apartment = Apartment::create($validated);

                // Automatically create management fee if area is provided and setting is enabled
                if ($apartment->area && ($request->has('create_management_fee') || Setting::getValue('management_fee_auto_generate', true))) {
                    try {
                        $this->managementFeeService->createManagementFeeForApartment(
                            $apartment,
                            $validated['management_fee_ratio'] ?? null,
                            $validated['sinking_fund_ratio'] ?? null
                        );
                        $message = 'Apartment created successfully with management fee calculations!';
                        if ($willAutoUpdate) {
                            $message .= ' Status automatically set to "Occupied".';
                        }
                        toast_success($message);
                    } catch (\Exception $e) {
                        $message = "Apartment created successfully, but failed to create management fee: " . $e->getMessage();
                        if ($willAutoUpdate) {
                            $message .= ' Status automatically set to "Occupied".';
                        }
                        toast_warning($message);
                    }
                } else {
                    toast_success('Apartment created successfully!');
                }

                return redirect()->route('apartments.show', $apartment);
            });

        } catch (\Exception $e) {
            toast_error('Failed to create apartment: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        $apartment->load([
            'managementCorporation', 
            'currentOwner', 
            'currentManagementFee',
            'managementFeeInvoices' => function($query) {
                $query->latest()->limit(5);
            },
            'invoices' => function($query) {
                $query->latest()->limit(5);
            }, 
            'maintenanceRequests' => function($query) {
                $query->latest()->limit(5);
            }
        ]);
        
        return view('apartments.show', compact('apartment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        $managementCorporations = ManagementCorporation::orderBy('name')->get();
        // Get actual owners from owners table with their user information
        $owners = \App\Models\Owner::with('user')->get();
        
        $typeOptions = ['1bhk', '2bhk', '3bhk', '4bhk', 'studio', 'penthouse'];
        $statusOptions = ['vacant', 'occupied', 'maintenance'];
        
        return view('apartments.edit', compact('apartment', 'managementCorporations', 'owners', 'typeOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:apartments,number,' . $apartment->id,
            'type' => 'required|in:1bhk,2bhk,3bhk,4bhk,studio,penthouse',
            'assessment_no' => 'nullable|string|max:50',
            'area' => 'nullable|numeric|min:1',
            'status' => 'required|in:vacant,occupied,maintenance',
            'description' => 'nullable|string',
            'management_corporation_id' => 'required|exists:management_corporations,id', // Required - apartments must have management corp
            'owner_id' => 'nullable|exists:owners,id', // Optional - apartments can exist without owners
            'rent_amount' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
        ]);

        // Check if owner assignment will auto-update status
        $oldOwnerId = $apartment->owner_id;
        $newOwnerId = $validated['owner_id'];
        $willAutoUpdate = false;
        
        if (!$oldOwnerId && $newOwnerId && $validated['status'] === 'vacant') {
            $willAutoUpdate = 'occupied';
        } elseif ($oldOwnerId && !$newOwnerId && $validated['status'] === 'occupied') {
            $willAutoUpdate = 'vacant';
        }

        $apartment->update($validated);

        // Provide appropriate success message
        if ($willAutoUpdate) {
            toast_success("Apartment updated successfully! Status automatically changed to \"" . ucfirst($willAutoUpdate) . "\".");
        } else {
            toast_success('Apartment updated successfully!');
        }
        
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
