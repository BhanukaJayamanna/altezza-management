<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Owner;
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
        } elseif ($user->isTenant()) {
            return $this->tenantDashboard();
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
            'total_tenants' => User::where('role', 'tenant')->where('status', 'active')->count(),
            'total_owners' => Owner::where('status', 'active')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'pending_maintenance' => MaintenanceRequest::where('status', 'pending')->count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
        ];
        
        $recentActivities = [
            'recent_invoices' => Invoice::with(['apartment', 'tenant'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_maintenance' => MaintenanceRequest::with(['apartment', 'tenant'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_complaints' => Complaint::with(['apartment', 'tenant'])
                ->latest()
                ->take(5)
                ->get(),
        ];
        
        return view('dashboard.admin', compact('stats', 'recentActivities'));
    }
    
    private function tenantDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        
        if (!$tenant) {
            return view('dashboard.tenant-no-apartment');
        }
        
        $stats = [
            'current_apartment' => $tenant->apartment,
            'pending_invoices' => $user->invoices()->where('status', 'pending')->count(),
            'overdue_invoices' => $user->invoices()->where('status', 'overdue')->count(),
            'maintenance_requests' => $user->maintenanceRequests()->where('status', '!=', 'completed')->count(),
            'open_complaints' => $user->complaints()->where('status', '!=', 'closed')->count(),
        ];
        
        $recentActivities = [
            'recent_invoices' => $user->invoices()
                ->with('apartment')
                ->latest()
                ->take(5)
                ->get(),
            'recent_maintenance' => $user->maintenanceRequests()
                ->with('apartment')
                ->latest()
                ->take(5)
                ->get(),
            'recent_complaints' => $user->complaints()
                ->with('apartment')
                ->latest()
                ->take(3)
                ->get(),
        ];
        
        return view('dashboard.tenant', compact('stats', 'recentActivities'));
    }
}
