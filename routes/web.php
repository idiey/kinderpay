<?php

use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeTemplateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Health Check Route (Fast, unauthenticated, zero session overhead)
Route::get('/healthz', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

// PWA Static Asset Deliveries
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), ['Content-Type' => 'application/manifest+json']);
});
Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), ['Content-Type' => 'application/javascript']);
});
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Public Welcome Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Webhooks (Public, CSRF-exempt)
Route::post('/api/webhooks/billplz', [WebhookController::class, 'billplz'])->name('api.webhooks.billplz');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin / Staff Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students Management
    Route::resource('students', StudentController::class);

    // Classes Management
    Route::resource('classes', ClassGroupController::class)->except(['create', 'show', 'edit']);

    // Fee Templates
    Route::resource('fee-templates', FeeTemplateController::class)->except(['create', 'show', 'edit']);

    // Fee Assignment
    Route::get('/fees/assign', [StudentFeeController::class, 'index'])->name('fees.assign');
    Route::post('/fees/assign', [StudentFeeController::class, 'store'])->name('fees.assign.store');
    Route::delete('/fees/assign/{studentFee}', [StudentFeeController::class, 'destroy'])->name('fees.assign.destroy');

    // Invoices Management
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::post('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // Payments Management
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/invoices/{invoice}/record-payment', [PaymentController::class, 'record'])->name('payments.record');
    Route::post('/invoices/{invoice}/record-payment', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');

    // Staff & HR Management
    Route::resource('staff', \App\Http\Controllers\StaffController::class);
    Route::post('/staff/{staff}/allowances', [\App\Http\Controllers\StaffController::class, 'addAllowance'])->name('staff.allowances.store');

    // Leave Management
    Route::get('/leave', [\App\Http\Controllers\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave', [\App\Http\Controllers\LeaveController::class, 'store'])->name('leave.store');
    Route::post('/leave/{leaveRequest}/review', [\App\Http\Controllers\LeaveController::class, 'review'])->name('leave.review');

    // Payroll Management
    Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/generate', [\App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('/payroll/{payrollRun}', [\App\Http\Controllers\PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{payrollRun}/confirm', [\App\Http\Controllers\PayrollController::class, 'confirm'])->name('payroll.confirm');
    Route::get('/payroll/item/{payrollItem}/payslip', [\App\Http\Controllers\PayrollController::class, 'downloadPayslip'])->name('payroll.payslip');

    // Finance Reports
    Route::get('/reports/finance', [\App\Http\Controllers\FinanceReportController::class, 'index'])->name('reports.finance');

    // Multi-Branch Kindergarten Management
    Route::get('/kindergartens', [\App\Http\Controllers\KindergartenController::class, 'index'])->name('kindergartens.index');
    Route::post('/kindergartens', [\App\Http\Controllers\KindergartenController::class, 'store'])->name('kindergartens.store');
    Route::put('/kindergartens/{kindergarten}', [\App\Http\Controllers\KindergartenController::class, 'update'])->name('kindergartens.update');
    Route::post('/tenants/switch', [\App\Http\Controllers\TenantSwitchController::class, 'switch'])->name('tenants.switch');

    // Kindergarten Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Parent Portal Routes
Route::middleware(['auth'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/invoices', [ParentPortalController::class, 'invoices'])->name('invoices');
    Route::get('/invoices/{invoice}', [ParentPortalController::class, 'showInvoice'])->name('invoices.show');
    Route::post('/invoices/{invoice}/pay', [ParentPortalController::class, 'pay'])->name('invoices.pay');
    Route::get('/history', [ParentPortalController::class, 'history'])->name('history');
    Route::get('/payments/{payment}/receipt', [ParentPortalController::class, 'downloadReceipt'])->name('payments.receipt');
});

require __DIR__.'/auth.php';
