<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::with('creator')
            ->latest()
            ->paginate(15);

        return view('notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,maintenance,emergency,policy',
            'priority' => 'required|in:low,medium,high',
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();

        Notice::create($validated);

        toast_success('Notice created successfully!');
        return redirect()->route('notices.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        $notice->load('creator');
        return view('notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        return view('notices.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,maintenance,emergency,policy',
            'priority' => 'required|in:low,medium,high',
            'is_urgent' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $notice->update($validated);

        toast_success('Notice updated successfully!');
        return redirect()->route('notices.show', $notice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();

        toast_success('Notice deleted successfully!');
        return redirect()->route('notices.index');
    }

    /**
     * Tenant-specific methods
     */
    public function tenantIndex()
    {
        $notices = Notice::where('published_at', '<=', now())
            ->with('creator')
            ->latest('published_at')
            ->paginate(15);

        return view('tenant.notices.index', compact('notices'));
    }
}
