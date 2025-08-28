<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\UtilityMeterController;
use App\Http\Controllers\UtilityReadingController;
use App\Http\Controllers\UtilityUnitPriceController;
use App\Http\Controllers\UtilityBillController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RooftopReservationController;
use App\Http\Controllers\ToastDemoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin and Manager routes
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('apartments', ApartmentController::class);
        Route::resource('owners', OwnerController::class);
        Route::resource('owners', OwnerController::class);
        Route::resource('leases', LeaseController::class);
        
        // Additional lease routes
        Route::patch('leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
        Route::patch('leases/{lease}/renew', [LeaseController::class, 'renew'])->name('leases.renew');
        
        Route::resource('invoices', InvoiceController::class);
        Route::resource('payments', PaymentController::class);
        Route::resource('maintenance-requests', MaintenanceRequestController::class);
        Route::resource('complaints', ComplaintController::class);
        Route::resource('notices', NoticeController::class);
        
        // Additional invoice routes
        Route::post('invoices/generate-monthly-rent', [InvoiceController::class, 'generateMonthlyRent'])->name('invoices.generate-monthly-rent');
        Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('invoices/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
        Route::get('invoices/{invoice}/preview-pdf', [InvoiceController::class, 'previewPdf'])->name('invoices.preview-pdf');
        Route::post('invoices/{invoice}/send-reminder', [InvoiceController::class, 'sendPaymentReminder'])->name('invoices.send-reminder');
        Route::post('invoices/send-bulk-reminders', [InvoiceController::class, 'sendBulkPaymentReminders'])->name('invoices.send-bulk-reminders');
        
        // Additional payment routes
        Route::patch('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::patch('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        
        // Utility Management Routes
        Route::prefix('utilities')->name('utility-')->group(function () {
            // Utility Meters
            Route::resource('meters', UtilityMeterController::class);
            Route::get('meters/apartment/{apartment}', [UtilityMeterController::class, 'byApartment'])
                ->name('meters.by-apartment');
            
            // Utility Readings
            Route::resource('readings', UtilityReadingController::class);
            Route::get('readings/bulk-entry', [UtilityReadingController::class, 'bulkEntry'])
                ->name('readings.bulk');
            Route::post('readings/bulk-store', [UtilityReadingController::class, 'bulkStore'])
                ->name('readings.bulk-store');
            Route::post('readings/generate-bills', [UtilityReadingController::class, 'generateBills'])
                ->name('readings.generate-bills');
            Route::get('readings/export', [UtilityReadingController::class, 'export'])
                ->name('readings.export');
            
            // Utility Unit Prices
            Route::resource('unit-prices', UtilityUnitPriceController::class);
            Route::get('unit-prices/current/active', [UtilityUnitPriceController::class, 'current'])
                ->name('unit-prices.current');
            
            // Utility Bills
            Route::resource('bills', UtilityBillController::class);
            Route::post('bills/{utilityBill}/mark-paid', [UtilityBillController::class, 'markAsPaid'])
                ->name('bills.mark-paid');
            Route::post('bills/{utilityBill}/process-payment', [UtilityBillController::class, 'processPayment'])
                ->name('bills.process-payment');
            Route::post('bills/{utilityBill}/generate-invoice', [UtilityBillController::class, 'generateInvoice'])
                ->name('bills.generate-invoice');
            Route::post('bills/bulk-generate-invoices', [UtilityBillController::class, 'bulkGenerateInvoices'])
                ->name('bills.bulk-generate-invoices');
            Route::get('bills/analytics/dashboard', [UtilityBillController::class, 'analytics'])
                ->name('bills.analytics');
            Route::post('bills/mark-overdue', [UtilityBillController::class, 'markOverdue'])
                ->name('bills.mark-overdue');
            Route::get('bills/generate/form', [UtilityBillController::class, 'generateBills'])
                ->name('bills.generate');
            Route::post('bills/generate/process', [UtilityBillController::class, 'processGenerateBills'])
                ->name('bills.process-generate');
            Route::get('bills/{utilityBill}/download-pdf', [UtilityBillController::class, 'downloadPdf'])
                ->name('bills.download-pdf');
            Route::get('bills/export', [UtilityBillController::class, 'export'])
                ->name('bills.export');
        });
        
        // Additional maintenance request routes
        Route::patch('maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign'])
            ->name('maintenance-requests.assign');
        Route::patch('maintenance-requests/{maintenanceRequest}/status', [MaintenanceRequestController::class, 'updateStatus'])
            ->name('maintenance-requests.update-status');
        
        // Additional complaint routes
        Route::patch('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])
            ->name('complaints.update-status');
        Route::patch('complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])
            ->name('complaints.resolve');
        
        // Payment Voucher Routes
        Route::resource('vouchers', PaymentVoucherController::class);
        Route::patch('vouchers/{voucher}/approve', [PaymentVoucherController::class, 'approve'])
            ->name('vouchers.approve');
        Route::patch('vouchers/{voucher}/reject', [PaymentVoucherController::class, 'reject'])
            ->name('vouchers.reject');
        Route::patch('vouchers/{voucher}/mark-paid', [PaymentVoucherController::class, 'markAsPaid'])
            ->name('vouchers.mark-paid');
        Route::get('vouchers/{voucher}/export-pdf', [PaymentVoucherController::class, 'exportPDF'])
            ->name('vouchers.export-pdf');
    });
    
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Analytics Dashboard (Admin/Manager Access)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('legacy.dashboard');
            
            // Advanced Analytics
            Route::get('/advanced', [AnalyticsController::class, 'getAdvancedAnalytics'])->name('advanced');
            Route::get('/real-time-metrics', [AnalyticsController::class, 'getRealTimeMetrics'])->name('real-time');
            Route::get('/chart-data', [AnalyticsController::class, 'getChartData'])->name('chart-data');
            Route::get('/benchmarks', [AnalyticsController::class, 'getBenchmarks'])->name('benchmarks');
            
            // Specialized Pages
            Route::get('/financial', [AnalyticsController::class, 'financial'])->name('financial');
            Route::get('/occupancy', [AnalyticsController::class, 'occupancy'])->name('occupancy');
            Route::get('/owners', [AnalyticsController::class, 'owners'])->name('owners');
            Route::get('/maintenance', [AnalyticsController::class, 'maintenance'])->name('maintenance');
            
            // Reports and Exports
            Route::post('/generate-report', [AnalyticsController::class, 'generateReport'])->name('generate-report');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
            
            // Cache Management
            Route::delete('/cache', [AnalyticsController::class, 'clearCache'])->name('clear-cache');
        });
        
        // System Settings (Admin Only)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/', [SettingController::class, 'update'])->name('update');
            Route::post('/reset', [SettingController::class, 'reset'])->name('reset');
            Route::get('/export', [SettingController::class, 'export'])->name('export');
            Route::post('/import', [SettingController::class, 'import'])->name('import');
            Route::post('/test-email', [SettingController::class, 'testEmail'])->name('test-email');
        });
    });
    
    // Owner routes (limited access)
    Route::middleware('role:owner')->group(function () {
        Route::get('my-apartment', [ApartmentController::class, 'show'])->name('owner.apartment');
        Route::get('my-invoices', [InvoiceController::class, 'index'])->name('owner.invoices');
        Route::get('my-invoices/{invoice}', [InvoiceController::class, 'show'])->name('owner.invoices.show');
        Route::get('my-invoices/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('owner.invoices.download-pdf');
        Route::get('my-invoices/{invoice}/preview-pdf', [InvoiceController::class, 'previewPdf'])->name('owner.invoices.preview-pdf');
        
        // Owner payment routes
        Route::get('my-payments', [PaymentController::class, 'index'])->name('owner.payments');
        Route::get('my-payments/create', [PaymentController::class, 'create'])->name('owner.payments.create');
        Route::post('my-payments', [PaymentController::class, 'store'])->name('owner.payments.store');
        Route::get('my-payments/{payment}', [PaymentController::class, 'show'])->name('owner.payments.show');
        
        // Owner utility bill routes
        Route::get('my-utility-bills', [UtilityBillController::class, 'ownerIndex'])->name('owner.utility-bills');
        Route::get('my-utility-bills/{utilityBill}', [UtilityBillController::class, 'ownerShow'])->name('owner.utility-bills.show');
        Route::get('my-utility-bills/{utilityBill}/download-pdf', [UtilityBillController::class, 'downloadPdf'])->name('owner.utility-bills.download-pdf');
        
        // Owner maintenance request routes
        Route::get('my-maintenance-requests', [MaintenanceRequestController::class, 'ownerIndex'])->name('owner.maintenance-requests');
        Route::get('my-maintenance-requests/create', [MaintenanceRequestController::class, 'ownerCreate'])->name('owner.maintenance-requests.create');
        Route::post('my-maintenance-requests', [MaintenanceRequestController::class, 'ownerStore'])->name('owner.maintenance-requests.store');
        Route::get('my-maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'ownerShow'])->name('owner.maintenance-requests.show');
        
        // Owner complaint routes
        Route::get('my-complaints', [ComplaintController::class, 'ownerIndex'])->name('owner.complaints');
        Route::get('my-complaints/create', [ComplaintController::class, 'ownerCreate'])->name('owner.complaints.create');
        Route::post('my-complaints', [ComplaintController::class, 'ownerStore'])->name('owner.complaints.store');
        Route::get('my-complaints/{complaint}', [ComplaintController::class, 'ownerShow'])->name('owner.complaints.show');
        
        // Owner notice routes
        Route::get('my-notices', [NoticeController::class, 'ownerIndex'])->name('owner.notices');
    });
    
    // Notification Routes (All authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/page', [NotificationController::class, 'page'])->name('page');
        Route::patch('{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // API Routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/test', [NotificationController::class, 'test'])->name('test');
        });
    });
    
    // Rooftop Reservation routes (Admin and Manager only)
    Route::middleware('role:admin,manager')->group(function () {
        Route::prefix('rooftop-reservations')->name('rooftop-reservations.')->group(function () {
            Route::get('/', [RooftopReservationController::class, 'index'])->name('index');
            Route::get('/create', [RooftopReservationController::class, 'create'])->name('create');
            Route::post('/', [RooftopReservationController::class, 'store'])->name('store');
            Route::get('/{rooftopReservation}', [RooftopReservationController::class, 'show'])->name('show');
            Route::get('/{rooftopReservation}/edit', [RooftopReservationController::class, 'edit'])->name('edit');
            Route::put('/{rooftopReservation}', [RooftopReservationController::class, 'update'])->name('update');
            Route::delete('/{rooftopReservation}', [RooftopReservationController::class, 'destroy'])->name('destroy');
            Route::patch('/{rooftopReservation}/approve', [RooftopReservationController::class, 'approve'])->name('approve');
            Route::patch('/{rooftopReservation}/cancel', [RooftopReservationController::class, 'cancel'])->name('cancel');
            Route::patch('/{rooftopReservation}/complete', [RooftopReservationController::class, 'complete'])->name('complete');
            Route::get('/{rooftopReservation}/download-pdf', [RooftopReservationController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/api/available-time-slots', [RooftopReservationController::class, 'getAvailableTimeSlots'])->name('available-time-slots');
        });
    });
    
    // Toast Demo Routes (Development Only) - Moved inside auth middleware
    Route::prefix('toast-demo')->name('toast.')->group(function () {
        Route::get('/', [ToastDemoController::class, 'index'])->name('demo');
        Route::post('/test', [ToastDemoController::class, 'demo'])->name('test');
        Route::post('/action', [ToastDemoController::class, 'actionToast'])->name('action');
    });
});

require __DIR__.'/auth.php';
