<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complaints = Complaint::with(['apartment', 'owner'])
            ->latest()
            ->paginate(15);

        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apartments = Apartment::with('currentOwner')->get();
        return view('complaints.create', compact('apartments'));
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
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string|max:100',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'open';

        Complaint::create($validated);

        toast_success('Complaint filed successfully!');
        return redirect()->route('complaints.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['apartment', 'owner']);
        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        $apartments = Apartment::with('currentOwner')->get();
        return view('complaints.edit', compact('complaint', 'apartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string|max:100',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $complaint->update($validated);

        toast_success('Complaint updated successfully!');
        return redirect()->route('complaints.show', $complaint);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        toast_success('Complaint deleted successfully!');
        return redirect()->route('complaints.index');
    }

    /**
     * Owner-specific methods
     */
    public function ownerIndex()
    {
        $user = Auth::user();
        $complaints = $user->complaints()
            ->with(['apartment'])
            ->latest()
            ->paginate(15);

        return view('owner.complaints.index', compact('complaints'));
    }

    public function ownerCreate()
    {
        $user = Auth::user();
        $owner = $user->owner;
        
        if (!$owner || !$owner->apartment) {
            toast_error('You must be assigned to an apartment to file complaints.');
            return redirect()->route('dashboard');
        }

        return view('owner.complaints.create', compact('owner'));
    }

    public function ownerStore(Request $request)
    {
        $user = Auth::user();
        $owner = $user->owner;
        
        if (!$owner || !$owner->apartment) {
            toast_error('You must be assigned to an apartment to file complaints.');
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|string|max:100',
        ]);

        $validated['apartment_id'] = $owner->apartment_id;
        $validated['owner_id'] = $user->id;
        $validated['status'] = 'open';

        Complaint::create($validated);

        toast_success('Complaint filed successfully!');
        return redirect()->route('owner.complaints');
    }

    public function ownerShow(Complaint $complaint)
    {
        // Ensure owner can only view their own complaints
        if ($complaint->owner_id !== Auth::id()) {
            abort(403);
        }

        $complaint->load(['apartment']);
        return view('owner.complaints.show', compact('complaint'));
    }
}
