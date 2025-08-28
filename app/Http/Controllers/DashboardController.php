<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Owner;
use App\Models\ManagementCorporation;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            return $this->adminManagerDashboard();
        } elseif ($user->isOwner()) {
            return $this->ownerDashboard();
        }
        
        return redirect()->route('login');
    }
    
    private function adminManagerDashboard()
    {
        $stats = [
            'total_apartments' => Apartment::count(),
            'occupied_apartments' => Apartment::where('status', 'occupied')->count(),
            'vacant_apartments' => Apartment::where('status', 'vacant')->count(),
            'maintenance_apartments' => Apartment::where('status', 'maintenance')->count(),
            'total_owners' => User::whereHas('roles', function($q) { $q->where('name', 'owner'); })->count(),
            'total_management_corporations' => ManagementCorporation::where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'pending_maintenance' => MaintenanceRequest::where('status', 'pending')->count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
        ];
        
        $recentActivities = [
            'recent_invoices' => Invoice::with(['apartment', 'owner'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_maintenance' => MaintenanceRequest::with(['apartment', 'owner'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_complaints' => Complaint::with(['apartment', 'owner'])
                ->latest()
                ->take(5)
                ->get(),
        ];
        
        return view('dashboard.admin', compact('stats', 'recentActivities'));
    }
    
    private function ownerDashboard()
    {
        $user = Auth::user();
        $owner = $user->ownerProfile;
        
        if (!$owner) {
            return view('dashboard.owner-no-apartment');
        }
        
        $stats = [
            'current_apartment' => $owner->apartment,
            'pending_invoices' => Invoice::where('owner_id', $user->id)->where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('owner_id', $user->id)->where('status', 'overdue')->count(),
            'maintenance_requests' => MaintenanceRequest::where('owner_id', $user->id)->where('status', '!=', 'completed')->count(),
            'open_complaints' => Complaint::where('owner_id', $user->id)->where('status', '!=', 'closed')->count(),
        ];
        
        $recentActivities = [
            'recent_invoices' => Invoice::where('owner_id', $user->id)
                ->with('apartment')
                ->latest()
                ->take(5)
                ->get(),
            'recent_maintenance' => MaintenanceRequest::where('owner_id', $user->id)
                ->with('apartment')
                ->latest()
                ->take(5)
                ->get(),
            'recent_complaints' => Complaint::where('owner_id', $user->id)
                ->with('apartment')
                ->latest()
                ->take(3)
                ->get(),
        ];
        
        return view('dashboard.owner', compact('stats', 'recentActivities'));
    }
}
