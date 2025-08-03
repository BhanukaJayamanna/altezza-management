<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip maintenance mode check for admin users
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Skip maintenance mode check for settings routes (admin only)
        if ($request->is('admin/settings*')) {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        if (Setting::isMaintenanceMode()) {
            $message = Setting::getValue('maintenance_message', 'System is under maintenance. Please try again later.');
            
            return response()->view('maintenance', [
                'message' => $message
            ], 503);
        }

        return $next($request);
    }
}
