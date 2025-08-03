<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ToastDemoController extends Controller
{
    /**
     * Show the toast demo page.
     */
    public function index(): View
    {
        return view('toast-demo');
    }
    
    /**
     * Handle demo toast requests.
     */
    public function demo(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'info');
        
        $messages = [
            'success' => [
                'Settings updated successfully!',
                'Rent payment processed successfully.',
                'Maintenance request completed.',
                'Invoice sent to tenant.',
                'Property added to system.'
            ],
            'error' => [
                'Failed to save settings. Please try again.',
                'Payment processing failed.',
                'Unable to assign maintenance request.',
                'Error sending invoice email.',
                'Validation failed - please check your input.'
            ],
            'warning' => [
                'Some settings require restart to take effect.',
                'Lease expires in 30 days.',
                'Payment is overdue by 5 days.',
                'Maintenance request is urgent.',
                'Utility meter reading is missing.'
            ],
            'info' => [
                'System backup completed at 2:00 AM.',
                'New tenant application received.',
                'Monthly report is ready for download.',
                'Maintenance scheduled for next week.',
                'Rent reminder sent to all tenants.'
            ]
        ];
        
        $typeMessages = $messages[$type] ?? $messages['info'];
        $randomMessage = $typeMessages[array_rand($typeMessages)];
        
        // Use the new toast helper functions
        switch ($type) {
            case 'success':
                toast_success($randomMessage);
                break;
            case 'error':
                toast_error($randomMessage);
                break;
            case 'warning':
                toast_warning($randomMessage);
                break;
            case 'info':
                toast_info($randomMessage);
                break;
        }
        
        return redirect()->route('toast.demo');
    }
    
    /**
     * Test action toast.
     */
    public function actionToast(): RedirectResponse
    {
        toast_with_action(
            'info',
            'Would you like to view the settings page?',
            'Go to Settings',
            route('settings.index'),
            'Action Required'
        );
        
        return redirect()->route('toast.demo');
    }
}
