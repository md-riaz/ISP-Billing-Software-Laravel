<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DuesController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OltDeviceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PopController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Platform\PlatformDashboardController;
use App\Http\Controllers\Platform\TenantController;
use App\Http\Controllers\Platform\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Platform ─────────────────────────────────────────────────────────────────
Route::prefix('platform')->name('platform.')->group(function () {
    Route::middleware(['auth', 'platform'])->group(function () {
        Route::get('/dashboard', [PlatformDashboardController::class, 'index'])->name('dashboard');
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/payment', [TenantController::class, 'recordPayment'])->name('tenants.payment');
        Route::resource('plans', SubscriptionPlanController::class);
    });
});

// ─── Tenant App ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'tenant', 'resolve.tenant'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Packages
    Route::resource('packages', PackageController::class)->except(['show']);

    // Areas & POPs
    Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::put('/areas/{area}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('/areas/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');

    Route::post('/pops', [PopController::class, 'store'])->name('pops.store');
    Route::put('/pops/{pop}', [PopController::class, 'update'])->name('pops.update');
    Route::delete('/pops/{pop}', [PopController::class, 'destroy'])->name('pops.destroy');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/status', [ServiceController::class, 'updateStatus'])->name('services.update-status');

    // OLT Devices
    Route::resource('olt-devices', OltDeviceController::class)->parameters(['olt-devices' => 'oltDevice']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::post('/invoices/generate-bulk', [InvoiceController::class, 'generateBulk'])->name('invoices.generate-bulk');
    Route::post('/invoices/generate-single', [InvoiceController::class, 'generateSingle'])->name('invoices.generate-single');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');

    // Dues
    Route::get('/dues', [DuesController::class, 'index'])->name('dues.index');

    // Reports
    Route::get('/reports/collections', [ReportController::class, 'collections'])->name('reports.collections');
    Route::get('/reports/billing', [ReportController::class, 'billing'])->name('reports.billing');

    // Staff
    Route::resource('staff', StaffController::class)->parameters(['staff' => 'staff']);

    // Settings
    Route::get('/settings/company', [SettingController::class, 'company'])->name('settings.company');
    Route::post('/settings/company', [SettingController::class, 'updateCompany'])->name('settings.company.update');

    // SMS
    Route::get('/sms/templates', [SmsController::class, 'templates'])->name('sms.templates');
    Route::post('/sms/templates', [SmsController::class, 'storeTemplate'])->name('sms.templates.store');
    Route::put('/sms/templates/{template}', [SmsController::class, 'updateTemplate'])->name('sms.templates.update');
    Route::delete('/sms/templates/{template}', [SmsController::class, 'destroyTemplate'])->name('sms.templates.destroy');
    Route::get('/sms/logs', [SmsController::class, 'logs'])->name('sms.logs');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
