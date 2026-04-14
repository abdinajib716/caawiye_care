<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\ActionLogController;
use App\Http\Controllers\Backend\Auth\ScreenshotGeneratorLoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmailTestController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\TermController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\UserLoginAsController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Admin routes.
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);
    Route::delete('roles/delete/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulk-delete');

    // Permissions Routes.
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');


    // Hospitals Routes.
    Route::get('hospitals/export', [App\Http\Controllers\Backend\HospitalController::class, 'export'])->name('hospitals.export');
    Route::post('hospitals/import', [App\Http\Controllers\Backend\HospitalController::class, 'import'])->name('hospitals.import');
    Route::get('hospitals/sample-template', [App\Http\Controllers\Backend\HospitalController::class, 'downloadSampleTemplate'])->name('hospitals.sample-template');
    Route::resource('hospitals', App\Http\Controllers\Backend\HospitalController::class);

    // Doctors Routes.
    Route::resource('doctors', App\Http\Controllers\Backend\DoctorController::class);
    Route::get('doctors-by-hospital/{hospital}', [App\Http\Controllers\Backend\DoctorController::class, 'getDoctorsByHospital'])->name('doctors.by-hospital');

    // Appointments Routes.
    Route::get('appointments', [App\Http\Controllers\Backend\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/export-pdf', [App\Http\Controllers\Backend\AppointmentController::class, 'exportPdf'])->name('appointments.export-pdf');
    Route::get('appointments/create', [App\Http\Controllers\Backend\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('appointments', [App\Http\Controllers\Backend\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('appointments/{appointment}', [App\Http\Controllers\Backend\AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('appointments/{appointment}/export-booking-pdf', [App\Http\Controllers\Backend\AppointmentController::class, 'exportBookingPdf'])->name('appointments.export-booking-pdf');
    Route::post('appointments/{appointment}/reschedule', [App\Http\Controllers\Backend\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('appointments/{appointment}/cancel', [App\Http\Controllers\Backend\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{appointment}/confirm', [App\Http\Controllers\Backend\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/complete', [App\Http\Controllers\Backend\AppointmentController::class, 'complete'])->name('appointments.complete');

    // Customers Routes.
    Route::get('customers/export', [App\Http\Controllers\Backend\CustomerController::class, 'export'])->name('customers.export');
    Route::post('customers/import', [App\Http\Controllers\Backend\CustomerController::class, 'import'])->name('customers.import');
    Route::get('customers/sample-template', [App\Http\Controllers\Backend\CustomerController::class, 'downloadSampleTemplate'])->name('customers.sample-template');
    Route::resource('customers', App\Http\Controllers\Backend\CustomerController::class);
    Route::delete('customers/bulk-delete', [App\Http\Controllers\Backend\CustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');
    Route::patch('customers/bulk-update-status', [App\Http\Controllers\Backend\CustomerController::class, 'bulkUpdateStatus'])->name('customers.bulk-update-status');



    // Orders Routes (View/Manage orders)
    Route::get('orders', [App\Http\Controllers\Backend\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [App\Http\Controllers\Backend\OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}', [App\Http\Controllers\Backend\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Backend\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/cancel', [App\Http\Controllers\Backend\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('orders/bulk-update-status', [App\Http\Controllers\Backend\OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-update-status');

    // Transactions Routes (View/Manage payment transactions)
    Route::get('transactions', [App\Http\Controllers\Backend\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [App\Http\Controllers\Backend\TransactionController::class, 'show'])->name('transactions.show');

    // Removed: Modules Routes

    // Settings Routes.
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    // Email testing routes
    Route::post('/settings/test-smtp', [SettingController::class, 'testSmtpConnection'])->name('settings.test-smtp');
    Route::post('/settings/send-test-email', [SettingController::class, 'sendTestEmail'])->name('settings.send-test-email');

    // Translation Routes.
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');

    // Login as & Switch back.
    Route::resource('users', UserController::class);
    Route::delete('users/delete/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
    Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

    // Action Log Routes.
    Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');

    // Email Test Routes.
    Route::prefix('email')->name('email.')->group(function () {
        Route::post('/test', [EmailTestController::class, 'sendTestEmail'])->name('test');
        Route::post('/test-connection', [EmailTestController::class, 'testConnection'])->name('test-connection');
        Route::get('/config-status', [EmailTestController::class, 'getConfigurationStatus'])->name('config-status');
        Route::get('/current-config', [EmailTestController::class, 'getCurrentConfig'])->name('current-config');
    });
    // Removed: Posts/Pages Routes, Terms Routes, and Media Routes

    // Editor Upload Route.
    Route::post('/editor/upload', [App\Http\Controllers\Backend\EditorController::class, 'upload'])->name('editor.upload');

    // AI Content Management Routes
    Route::group(['prefix' => 'ai-content', 'as' => 'ai-content.'], function () {
        Route::get('/', [App\Http\Controllers\Backend\AiContentController::class, 'index'])->name('index');
        Route::get('/providers', [App\Http\Controllers\Backend\AiContentController::class, 'getProviders'])->name('providers');
        Route::post('/generate-content', [App\Http\Controllers\Backend\AiContentController::class, 'generateContent'])->name('generate-content');
    });

    // Medicine Orders Routes
    Route::resource('medicine-orders', App\Http\Controllers\Backend\MedicineOrderController::class)->only(['index', 'create', 'show']);
    Route::patch('medicine-orders/{medicineOrder}/status', [App\Http\Controllers\Backend\MedicineOrderController::class, 'updateStatus'])->name('medicine-orders.update-status');
    
    // Medicine Items Routes
    Route::resource('medicines', App\Http\Controllers\Backend\MedicineController::class);
    
    // Suppliers Routes
    Route::resource('suppliers', App\Http\Controllers\Backend\SupplierController::class);
    
    // Delivery Settings Routes
    Route::prefix('delivery-settings')->name('delivery-settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Backend\DeliverySettingController::class, 'index'])->name('index');
        Route::post('/locations', [App\Http\Controllers\Backend\DeliverySettingController::class, 'storeLocation'])->name('locations.store');
        Route::delete('/locations/{location}', [App\Http\Controllers\Backend\DeliverySettingController::class, 'destroyLocation'])->name('locations.destroy');
        Route::post('/prices', [App\Http\Controllers\Backend\DeliverySettingController::class, 'storePrice'])->name('prices.store');
        Route::delete('/prices/{price}', [App\Http\Controllers\Backend\DeliverySettingController::class, 'destroyPrice'])->name('prices.destroy');
    });

    // Providers Routes
    Route::get('providers/export', [App\Http\Controllers\Backend\ProviderController::class, 'export'])->name('providers.export');
    Route::post('providers/import', [App\Http\Controllers\Backend\ProviderController::class, 'import'])->name('providers.import');
    Route::get('providers/sample-template', [App\Http\Controllers\Backend\ProviderController::class, 'downloadSampleTemplate'])->name('providers.sample-template');
    Route::resource('providers', App\Http\Controllers\Backend\ProviderController::class);

    // Lab Tests Routes
    Route::get('lab-tests/export', [App\Http\Controllers\Backend\LabTestController::class, 'export'])->name('lab-tests.export');
    Route::post('lab-tests/import', [App\Http\Controllers\Backend\LabTestController::class, 'import'])->name('lab-tests.import');
    Route::get('lab-tests/sample-template', [App\Http\Controllers\Backend\LabTestController::class, 'downloadSampleTemplate'])->name('lab-tests.sample-template');
    Route::resource('lab-tests', App\Http\Controllers\Backend\LabTestController::class);

    // Lab Test Bookings Routes
    Route::get('lab-test-bookings', [App\Http\Controllers\Backend\LabTestBookingController::class, 'index'])->name('lab-test-bookings.index');
    Route::get('lab-test-bookings/export-pdf', [App\Http\Controllers\Backend\LabTestBookingController::class, 'exportPdf'])->name('lab-test-bookings.export-pdf');
    Route::get('lab-test-bookings/create', [App\Http\Controllers\Backend\LabTestBookingController::class, 'create'])->name('lab-test-bookings.create');
    Route::get('lab-test-bookings/{labTestBooking}', [App\Http\Controllers\Backend\LabTestBookingController::class, 'show'])->name('lab-test-bookings.show');
    Route::post('lab-test-bookings/{labTestBooking}/confirm', [App\Http\Controllers\Backend\LabTestBookingController::class, 'confirm'])->name('lab-test-bookings.confirm');
    Route::post('lab-test-bookings/{labTestBooking}/mark-in-progress', [App\Http\Controllers\Backend\LabTestBookingController::class, 'markInProgress'])->name('lab-test-bookings.mark-in-progress');
    Route::post('lab-test-bookings/{labTestBooking}/complete', [App\Http\Controllers\Backend\LabTestBookingController::class, 'complete'])->name('lab-test-bookings.complete');
    Route::post('lab-test-bookings/{labTestBooking}/cancel', [App\Http\Controllers\Backend\LabTestBookingController::class, 'cancel'])->name('lab-test-bookings.cancel');

    // Scan & Imaging Services Routes
    Route::get('scan-imaging-services/export', [App\Http\Controllers\Backend\ScanImagingServiceController::class, 'export'])->name('scan-imaging-services.export');
    Route::post('scan-imaging-services/import', [App\Http\Controllers\Backend\ScanImagingServiceController::class, 'import'])->name('scan-imaging-services.import');
    Route::get('scan-imaging-services/sample-template', [App\Http\Controllers\Backend\ScanImagingServiceController::class, 'downloadSampleTemplate'])->name('scan-imaging-services.sample-template');
    Route::resource('scan-imaging-services', App\Http\Controllers\Backend\ScanImagingServiceController::class);

    // Scan & Imaging Bookings Routes
    Route::get('scan-imaging-bookings', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'index'])->name('scan-imaging-bookings.index');
    Route::get('scan-imaging-bookings/export-pdf', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'exportPdf'])->name('scan-imaging-bookings.export-pdf');
    Route::get('scan-imaging-bookings/create', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'create'])->name('scan-imaging-bookings.create');
    Route::get('scan-imaging-bookings/{scanImagingBooking}', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'show'])->name('scan-imaging-bookings.show');
    Route::post('scan-imaging-bookings/{scanImagingBooking}/confirm', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'confirm'])->name('scan-imaging-bookings.confirm');
    Route::post('scan-imaging-bookings/{scanImagingBooking}/mark-in-progress', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'markInProgress'])->name('scan-imaging-bookings.mark-in-progress');
    Route::post('scan-imaging-bookings/{scanImagingBooking}/complete', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'complete'])->name('scan-imaging-bookings.complete');
    Route::post('scan-imaging-bookings/{scanImagingBooking}/cancel', [App\Http\Controllers\Backend\ScanImagingBookingController::class, 'cancel'])->name('scan-imaging-bookings.cancel');

    // Collections Routes (Report Collections)
    Route::get('collections', [App\Http\Controllers\Backend\CollectionController::class, 'index'])->name('collections.index');
    Route::get('collections/create', [App\Http\Controllers\Backend\CollectionController::class, 'create'])->name('collections.create');
    Route::get('collections/settings', [App\Http\Controllers\Backend\CollectionController::class, 'settings'])->name('collections.settings');
    Route::put('collections/settings', [App\Http\Controllers\Backend\CollectionController::class, 'updateSettings'])->name('collections.settings.update');
    Route::get('collections/{id}', [App\Http\Controllers\Backend\CollectionController::class, 'show'])->name('collections.show');

    // Expense Categories Routes
    Route::resource('expense-categories', App\Http\Controllers\Backend\ExpenseCategoryController::class)->except(['show']);

    // Expenses Routes
    Route::get('expenses', [App\Http\Controllers\Backend\ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [App\Http\Controllers\Backend\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [App\Http\Controllers\Backend\ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}', [App\Http\Controllers\Backend\ExpenseController::class, 'show'])->name('expenses.show');
    Route::get('expenses/{expense}/edit', [App\Http\Controllers\Backend\ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('expenses/{expense}', [App\Http\Controllers\Backend\ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [App\Http\Controllers\Backend\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('expenses/{expense}/submit-for-approval', [App\Http\Controllers\Backend\ExpenseController::class, 'submitForApproval'])->name('expenses.submit-for-approval');
    Route::post('expenses/{expense}/approve', [App\Http\Controllers\Backend\ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [App\Http\Controllers\Backend\ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('expenses/{expense}/mark-as-paid', [App\Http\Controllers\Backend\ExpenseController::class, 'markAsPaid'])->name('expenses.mark-as-paid');

    // Refunds Routes
    Route::get('refunds', [App\Http\Controllers\Backend\RefundController::class, 'index'])->name('refunds.index');
    Route::get('refunds/create', [App\Http\Controllers\Backend\RefundController::class, 'create'])->name('refunds.create');
    Route::post('refunds', [App\Http\Controllers\Backend\RefundController::class, 'store'])->name('refunds.store');
    Route::get('refunds/{refund}', [App\Http\Controllers\Backend\RefundController::class, 'show'])->name('refunds.show');
    Route::post('refunds/{refund}/confirm-provider-reversed', [App\Http\Controllers\Backend\RefundController::class, 'confirmProviderReversed'])->name('refunds.confirm-provider-reversed');
    Route::post('refunds/{refund}/approve', [App\Http\Controllers\Backend\RefundController::class, 'approve'])->name('refunds.approve');
    Route::post('refunds/{refund}/reject', [App\Http\Controllers\Backend\RefundController::class, 'reject'])->name('refunds.reject');
    Route::post('refunds/{refund}/process', [App\Http\Controllers\Backend\RefundController::class, 'process'])->name('refunds.process');
    Route::post('refunds/{refund}/complete', [App\Http\Controllers\Backend\RefundController::class, 'complete'])->name('refunds.complete');

    // Financial Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Backend\FinancialReportController::class, 'index'])->name('index');
        Route::get('/revenue', [App\Http\Controllers\Backend\FinancialReportController::class, 'revenue'])->name('revenue');
        Route::get('/expenses', [App\Http\Controllers\Backend\FinancialReportController::class, 'expenses'])->name('expenses');
        Route::get('/provider-payouts', [App\Http\Controllers\Backend\FinancialReportController::class, 'providerPayouts'])->name('provider-payouts');
        Route::get('/profit-loss', [App\Http\Controllers\Backend\FinancialReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/export-revenue-pdf', [App\Http\Controllers\Backend\FinancialReportController::class, 'exportRevenuePdf'])->name('export-revenue-pdf');
        Route::get('/export-expenses-pdf', [App\Http\Controllers\Backend\FinancialReportController::class, 'exportExpensesPdf'])->name('export-expenses-pdf');
        Route::get('/export-profit-loss-pdf', [App\Http\Controllers\Backend\FinancialReportController::class, 'exportProfitLossPdf'])->name('export-profit-loss-pdf');
    });
});

/**
 * Profile routes.
 */
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/update-additional', [ProfileController::class, 'updateAdditional'])->name('update.additional');
});

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/screenshot-login/{email}', [ScreenshotGeneratorLoginController::class, 'login'])->middleware('web')->name('screenshot.login');
