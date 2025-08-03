<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\TenantController;
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
use App\Http\Controllers\ToastDemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
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
        Route::resource('tenants', TenantController::class);
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
    
    // Tenant routes (limited access)
    Route::middleware('role:tenant')->group(function () {
        Route::get('my-apartment', [ApartmentController::class, 'show'])->name('tenant.apartment');
        Route::get('my-invoices', [InvoiceController::class, 'index'])->name('tenant.invoices');
        Route::get('my-invoices/{invoice}', [InvoiceController::class, 'show'])->name('tenant.invoices.show');
        
        // Tenant payment routes
        Route::get('my-payments', [PaymentController::class, 'index'])->name('tenant.payments');
        Route::get('my-payments/create', [PaymentController::class, 'create'])->name('tenant.payments.create');
        Route::post('my-payments', [PaymentController::class, 'store'])->name('tenant.payments.store');
        Route::get('my-payments/{payment}', [PaymentController::class, 'show'])->name('tenant.payments.show');
        
        // Tenant utility bill routes
        Route::get('my-utility-bills', [UtilityBillController::class, 'tenantIndex'])->name('tenant.utility-bills');
        Route::get('my-utility-bills/{utilityBill}', [UtilityBillController::class, 'tenantShow'])->name('tenant.utility-bills.show');
        Route::get('my-utility-bills/{utilityBill}/download-pdf', [UtilityBillController::class, 'downloadPdf'])->name('tenant.utility-bills.download-pdf');
        
        // Tenant maintenance request routes
        Route::get('my-maintenance-requests', [MaintenanceRequestController::class, 'tenantIndex'])->name('tenant.maintenance-requests');
        Route::get('my-maintenance-requests/create', [MaintenanceRequestController::class, 'tenantCreate'])->name('tenant.maintenance-requests.create');
        Route::post('my-maintenance-requests', [MaintenanceRequestController::class, 'tenantStore'])->name('tenant.maintenance-requests.store');
        Route::get('my-maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'tenantShow'])->name('tenant.maintenance-requests.show');
        
        // Tenant complaint routes
        Route::get('my-complaints', [ComplaintController::class, 'tenantIndex'])->name('tenant.complaints');
        Route::get('my-complaints/create', [ComplaintController::class, 'tenantCreate'])->name('tenant.complaints.create');
        Route::post('my-complaints', [ComplaintController::class, 'tenantStore'])->name('tenant.complaints.store');
        Route::get('my-complaints/{complaint}', [ComplaintController::class, 'tenantShow'])->name('tenant.complaints.show');
        
        // Tenant notice routes
        Route::get('my-notices', [NoticeController::class, 'tenantIndex'])->name('tenant.notices');
    });
    
    // Notification Routes (All authenticated users)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    
    // Toast Demo Routes (Development Only)
    Route::prefix('toast-demo')->name('toast.')->group(function () {
        Route::get('/', [ToastDemoController::class, 'index'])->name('demo');
        Route::post('/test', [ToastDemoController::class, 'demo'])->name('test');
        Route::post('/action', [ToastDemoController::class, 'actionToast'])->name('action');
    });
});

require __DIR__.'/auth.php';
