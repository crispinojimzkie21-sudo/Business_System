<?php



use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\AdminController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\SuperAdminController;

use App\Http\Controllers\AdminManagementController;

use App\Http\Controllers\ProductController;

use App\Http\Controllers\SalesController;

use App\Http\Controllers\AttendanceController;

use App\Http\Controllers\EloadController;

use App\Http\Controllers\TelevisionEloadController;

use App\Http\Controllers\UserAccessController;

use App\Http\Controllers\BackupController;



Route::get('/', function () {

    return view('welcome');

});



// Test route for debugging

Route::get('/test', function () {

    return '✅ Laravel is working! Routes are properly registered.';

});



// Admin test route

Route::get('/test-admin', function () {

    if (Auth::check()) {

        $user = Auth::user();

        return [

            'authenticated' => true,

            'user_id' => $user->id,

            'email' => $user->email,

            'role' => $user->role,

            'is_admin' => $user->isAdmin(),

            'is_super_admin' => $user->isSuperAdmin()

        ];

    } else {

        return ['authenticated' => false, 'message' => 'Not logged in'];

    }

})->middleware(['web', 'auth']);



// Debug login route

Route::get('/debug-login', function () {

    return [

        'auth_check' => Auth::check(),

        'user' => Auth::user(),

        'session_id' => session()->getId(),

        'session_data' => session()->all()

    ];

})->middleware(['web', 'auth']);



// Test success message

Route::get('/test-success', function () {

    return redirect('/')->with('success', 'This is a test success message! The system is working properly.');

});



// Test CSRF token

Route::get('/test-csrf', function () {

    return [

        'csrf_token' => csrf_token(),

        'session_id' => session()->getId(),

        'session_exists' => session()->isStarted(),

    ];

});



// Test URL generation

Route::get('/test-urls', function () {

    return [

        'app_url' => config('app.url'),

        'url_helper' => url('/login'),

        'route_helper' => route('login.post'),

        'request_url' => request()->getSchemeAndHttpHost(),

    ];

});



// REMOVED: Broken UserAccessController routes - use /superadmin/access-control instead



// Super Admin shortcut route

Route::get('/superadmin', [SuperAdminController::class, 'index'])

    ->middleware(['web', 'auth', 'role:super_admin,admin'])

    ->name('superadmin.shortcut');



// Authentication

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('web');

Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('web');



// Admin Authentication

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login')->middleware('web');

Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post')->middleware('web');



// Super Admin Authentication

Route::get('/superadmin/login', [AuthController::class, 'showSuperAdminLogin'])->name('superadmin.login')->middleware('web');

Route::post('/superadmin/login', [AuthController::class, 'superAdminLogin'])->name('superadmin.login.post')->middleware('web');



// Contact form submission

Route::post('/contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit')->middleware('web');



// Registration disabled: routes removed to prevent public sign-up



Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('web');



// Attendance routes - available to all authenticated users (employee, admin, super_admin)

Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin')->middleware(['web', 'auth']);

Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout')->middleware(['web', 'auth']);



// Dashboard routing

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['web', 'auth'])->name('dashboard');



Route::get('/dashboard/admin', [AdminController::class, 'index'])

    ->middleware(['web', 'auth', 'role:admin', 'check.access'])

    ->name('dashboard.admin');



Route::get('/dashboard/super-admin', [SuperAdminController::class, 'index'])

    ->middleware(['web', 'auth', 'role:super_admin,admin'])

    ->name('dashboard.superadmin');



// Employee Dashboard (accessible by all authenticated users)

Route::get('/dashboard/employee', [UserController::class, 'index'])

    ->middleware(['web', 'auth'])

    ->name('dashboard.employee');



// User Dashboard (alias for employee dashboard)

Route::get('/dashboard/user', [UserController::class, 'index'])

    ->middleware(['web', 'auth'])

    ->name('dashboard.user');



// Manager Dashboard

Route::get('/dashboard/manager', [UserController::class, 'manager'])

    ->middleware(['web', 'auth', 'role:manager'])

    ->name('dashboard.manager');



// Cashier Dashboard (accessible by all authenticated users)

Route::get('/dashboard/cashier', [UserController::class, 'cashier'])

    ->middleware(['web', 'auth'])

    ->name('dashboard.cashier');



// Cashier routes (accessible by all authenticated users)

Route::middleware(['web', 'auth'])->group(function () {

    // Sales Management - Unique URLs for cashiers

    Route::get('/cashier/sales', [SalesController::class, 'index'])->name('cashier.sales.index');

    Route::get('/cashier/sales/create', [SalesController::class, 'create'])->name('cashier.sales.create');

    Route::post('/cashier/sales', [SalesController::class, 'store'])->name('cashier.sales.store');

    Route::get('/cashier/sales/history', [SalesController::class, 'history'])->name('cashier.sales.history');

    Route::get('/cashier/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('cashier.sales.receipt');

    Route::get('/cashier/sales/{sale}/resend-receipt', [SalesController::class, 'resendReceipt'])->name('cashier.sales.resend-receipt');

});



// Sales Clerk/Manager/Employee routes (cannot process sales - view only)

Route::middleware(['web', 'auth', 'role:employee|manager'])->group(function () {

    // Sales - View Only (no create/store)

    Route::get('/sales', [SalesController::class, 'index'])->name('employee.sales.index');

    Route::get('/sales/history', [SalesController::class, 'history'])->name('employee.sales.history');

    Route::get('/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('employee.sales.receipt');

});



// Products & Inventory - View Only (all authenticated users)

Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');

    

    // Universal Profile Routes - Available to all authenticated users

    Route::get('/profile', [UserController::class, 'profile'])->name('dashboard.profile');

    Route::post('/profile/password', [UserController::class, 'updatePassword'])->name('profile.update-password');

});



// Attendance routes - viewable by all authenticated users

Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware(['web', 'auth']);

Route::get('/attendance/records', [AttendanceController::class, 'records'])->name('attendance.records')->middleware(['web', 'auth']);



// Attendance Export Routes (Super Admin only)

Route::get('/attendance/export/pdf', [SuperAdminController::class, 'exportToPDF'])

    ->middleware(['web', 'auth', 'role:super_admin'])

    ->name('attendance.export.pdf');



Route::get('/attendance/export/excel', [SuperAdminController::class, 'exportToExcel'])

    ->middleware(['web', 'auth', 'role:super_admin'])

    ->name('attendance.export.excel');



Route::get('/attendance/print', [SuperAdminController::class, 'printAttendance'])

    ->middleware(['web', 'auth', 'role:super_admin'])

    ->name('attendance.print');



// Admin check-out route - for admins to check out any user

Route::post('/attendance/{userId}/admin-checkout', [AttendanceController::class, 'adminCheckOut'])

    ->name('attendance.admin-checkout')

    ->middleware(['web', 'auth']);



// Super Admin: Complete System Management

Route::get('/superadmin/access-control', [SuperAdminController::class, 'accessControl'])
    ->middleware(['web', 'auth', 'role:super_admin,admin'])
    ->name('superadmin.access-control');
Route::get('/superadmin/monthly-attendance', [SuperAdminController::class, 'monthlyAttendance'])
    ->middleware(['web', 'auth', 'role:super_admin,admin'])
    ->name('superadmin.monthly-attendance');
Route::get('/superadmin/refresh-attendances', [SuperAdminController::class, 'refreshAttendances'])
    ->middleware(['web', 'auth', 'role:super_admin,admin'])
    ->name('superadmin.refresh-attendances');
Route::get('/superadmin/real-time-stats', [SuperAdminController::class, 'realTimeStats'])
    ->middleware(['web', 'auth', 'role:super_admin,admin'])
    ->name('superadmin.real-time-stats');

// Backup Management Routes (Super Admin only)

Route::prefix('admin/backup')->middleware(['web', 'auth', 'role:super_admin'])->group(function () {
    // ... rest of the code remains the same ...

    Route::post('/create', [BackupController::class, 'create'])->name('admin.backup.create');

    Route::get('/list', [BackupController::class, 'list'])->name('admin.backup.list');

    Route::get('/download/{filename}', [BackupController::class, 'download'])->name('admin.backup.download');

    Route::delete('/delete/{filename}', [BackupController::class, 'delete'])->name('admin.backup.delete');

});



Route::patch('/superadmin/access/{id}/toggle', [AdminManagementController::class, 'toggleAccess'])

    ->middleware(['web', 'auth', 'role:super_admin,admin'])

    ->name('superadmin.toggle-access');



Route::middleware(['web', 'auth', 'role:super_admin,admin'])->group(function () {

    // User Management

    Route::get('/admin/register', [AdminManagementController::class, 'showRegister'])->name('admin.register.show');

    Route::post('/admin/register', [AdminManagementController::class, 'register'])->name('admin.register.store');

    Route::get('/admin/list', [AdminManagementController::class, 'listAdmins'])->name('admin.list');

    Route::get('/admin/{id}/edit', [AdminManagementController::class, 'editAdmin'])->name('admin.edit');

    Route::put('/admin/{id}', [AdminManagementController::class, 'updateAdmin'])->name('admin.update');

    Route::delete('/admin/{id}', [AdminManagementController::class, 'deleteAdmin'])->name('admin.destroy');

    

    // Profile Management

    Route::get('/profiles/all', [AdminManagementController::class, 'allProfiles'])->name('profiles.all');

    Route::get('/profile/{id}', [AdminManagementController::class, 'profile'])->name('profile.view');

    Route::get('/employee/register', [AdminManagementController::class, 'showEmployeeRegister'])->name('employee.register.show');

    Route::post('/employee/register', [AdminManagementController::class, 'registerEmployee'])->name('employee.register.store');

    Route::get('/employee/list', [AdminManagementController::class, 'listEmployees'])->name('employee.list');

    

    // User Edit/Delete Routes

    Route::get('/employee/{id}/edit', [AdminManagementController::class, 'editUser'])->name('employee.edit');

    Route::put('/employee/{id}', [AdminManagementController::class, 'updateUser'])->name('employee.update');

    Route::delete('/employee/{id}', [AdminManagementController::class, 'deleteUser'])->name('employee.destroy');

    

    // Product Management - Use universal routes for all roles

    // Products and Inventory are accessible via universal routes defined above

    

    // Sales Management

    Route::get('/superadmin/sales', [SalesController::class, 'index'])->name('superadmin.sales.index');

    Route::get('/superadmin/sales/create', [SalesController::class, 'create'])->name('superadmin.sales.create');

    Route::post('/superadmin/sales', [SalesController::class, 'store'])->name('superadmin.sales.store');

    Route::get('/superadmin/sales/reports', [SalesController::class, 'reports'])->name('superadmin.sales.reports');

    Route::get('/superadmin/sales/history', [SalesController::class, 'history'])->name('superadmin.sales.history');

    Route::get('/superadmin/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('superadmin.sales.receipt');

    Route::get('/superadmin/sales/{sale}/resend-receipt', [SalesController::class, 'resendReceipt'])->name('superadmin.sales.resend-receipt');

    Route::delete('/superadmin/sales/{sale}', [SalesController::class, 'destroy'])->name('superadmin.sales.destroy');

    

    // Attendance Management - Use universal routes for all roles

    // Attendance checkin/checkout are accessible via universal routes defined above

    

    // Attendance Management - Delete/Restore (Super Admin Only)

    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    Route::post('/attendance/{id}/restore', [AttendanceController::class, 'restore'])->name('attendance.restore');

    Route::delete('/attendance/{id}/force-delete', [AttendanceController::class, 'forceDelete'])->name('attendance.force-delete');

    Route::get('/attendance/trashed', [AttendanceController::class, 'trashed'])->name('attendance.trashed');

    Route::post('/attendance/bulk-delete', [AttendanceController::class, 'bulkDelete'])->name('attendance.bulk-delete');

    

    // E-Load Management (Super Admin - Full Access)

    // Category Management - Super Admin Only

    Route::get('/eload/categories', [EloadController::class, 'categoriesIndex'])->name('eload.categories.index');

    Route::get('/eload/categories/create', [EloadController::class, 'categoriesCreate'])->name('eload.categories.create');

    Route::post('/eload/categories', [EloadController::class, 'categoriesStore'])->name('eload.categories.store');

    Route::get('/eload/categories/{category}/edit', [EloadController::class, 'categoriesEdit'])->name('eload.categories.edit');

    Route::put('/eload/categories/{category}', [EloadController::class, 'categoriesUpdate'])->name('eload.categories.update');

    Route::delete('/eload/categories/{category}', [EloadController::class, 'categoriesDestroy'])->name('eload.categories.destroy');

    

    // E-Load Numbers - Super Admin Only (View Only)

    Route::get('/eload/numbers', [EloadController::class, 'numbersIndex'])->name('eload.numbers.index');

    

    // E-Load Products - Super Admin Only (Full CRUD)

    Route::get('/eload', [EloadController::class, 'index'])->name('eload.index');

    Route::get('/eload/create', [EloadController::class, 'create'])->name('eload.create');

    Route::post('/eload', [EloadController::class, 'store'])->name('eload.store');

    Route::get('/eload/{eload}/edit', [EloadController::class, 'edit'])->name('eload.edit');

    Route::put('/eload/{eload}', [EloadController::class, 'update'])->name('eload.update');

    Route::delete('/eload/{eload}', [EloadController::class, 'destroy'])->name('eload.destroy');

    

    // E-Load Transactions - Super Admin Only

    Route::get('/superadmin/eload/add-load', [EloadController::class, 'addLoad'])->name('eload.add-load');

    Route::get('/superadmin/eload/add-load-multiple', [EloadController::class, 'addLoadMultiple'])->name('eload.add-load-multiple');

    Route::post('/superadmin/eload/process-load', [EloadController::class, 'processLoad'])->name('eload.process-load');

    Route::post('/superadmin/eload/process-multiple-loads', [EloadController::class, 'processMultipleLoads'])->name('eload.process-multiple-loads');

    Route::get('/superadmin/eload/transactions/history', [EloadController::class, 'transactionsHistory'])->name('eload.transactions.history');

    Route::put('/superadmin/eload/transactions/{transaction}/status', [EloadController::class, 'updateTransactionStatus'])->name('eload.transactions.update-status');

});



// Admin: E-Load Management (Limited Access)

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {

    // User Management
    Route::delete('/employee/{id}', [AdminManagementController::class, 'deleteUser'])->name('employee.destroy');

    

    // Product Management - Use universal routes for all roles

    // Products and Inventory are accessible via universal routes defined above

    

    // Sales Management (View Only - No Delete)

    Route::get('/admin/sales', [SalesController::class, 'index'])->name('admin.sales.index');

    Route::get('/admin/sales/create', [SalesController::class, 'create'])->name('admin.sales.create');

    Route::post('/admin/sales', [SalesController::class, 'store'])->name('admin.sales.store');

    Route::get('/admin/sales/reports', [SalesController::class, 'reports'])->name('admin.sales.reports');

    Route::get('/admin/sales/history', [SalesController::class, 'history'])->name('admin.sales.history');

    Route::get('/admin/sales/{sale}/receipt', [SalesController::class, 'receipt'])->name('admin.sales.receipt');

    Route::get('/admin/sales/{sale}/resend-receipt', [SalesController::class, 'resendReceipt'])->name('admin.sales.resend-receipt');

    

    // Attendance Management - Use universal routes for all roles

    // Attendance checkin/checkout are accessible via universal routes defined above

    

    // Attendance Management - Delete/Restore

    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    Route::post('/attendance/{id}/restore', [AttendanceController::class, 'restore'])->name('attendance.restore');

    Route::delete('/attendance/{id}/force-delete', [AttendanceController::class, 'forceDelete'])->name('attendance.force-delete');

    Route::get('/attendance/trashed', [AttendanceController::class, 'trashed'])->name('attendance.trashed');

    Route::post('/attendance/bulk-delete', [AttendanceController::class, 'bulkDelete'])->name('attendance.bulk-delete');

    

    // E-Load Management (Admin - Limited Access)

    // E-Load Products - Admin Access (CRUD but no delete)

    Route::get('/eload', [EloadController::class, 'index'])->name('eload.index');

    Route::get('/eload/create', [EloadController::class, 'create'])->name('eload.create');

    Route::post('/eload', [EloadController::class, 'store'])->name('eload.store');

    Route::get('/eload/{eload}/edit', [EloadController::class, 'edit'])->name('eload.edit');

    // E-Load Transactions - Admin Access

    Route::get('/admin/eload/add-load', [EloadController::class, 'adminAddLoad'])->name('admin.eload.add-load');
    Route::get('/admin/eload/add-load-multiple', [EloadController::class, 'adminAddLoadMultiple'])->name('admin.eload.add-load-multiple');

    Route::get('/eload/add-load', [EloadController::class, 'addLoad'])->name('admin.eload.add-load.legacy');
    Route::get('/eload/add-load-multiple', [EloadController::class, 'addLoadMultiple'])->name('admin.eload.add-load-multiple.legacy');

    Route::post('/admin/eload/process-load', [EloadController::class, 'adminProcessLoad'])->name('admin.eload.process-load');
    Route::post('/admin/eload/process-multiple-loads', [EloadController::class, 'adminProcessMultipleLoads'])->name('admin.eload.process-multiple-loads');
    
    Route::post('/eload/process-load', [EloadController::class, 'processLoad'])->name('admin.eload.process-load.legacy');
    Route::post('/eload/process-multiple-loads', [EloadController::class, 'processMultipleLoads'])->name('admin.eload.process-multiple-loads.legacy');
    
    Route::get('/eload/transactions/history', [EloadController::class, 'transactionsHistory'])->name('admin.eload.transactions.history');

    Route::put('/eload/transactions/{transaction}/status', [EloadController::class, 'updateTransactionStatus'])->name('admin.eload.transactions.update-status');

});
// Admin TV E-Load Dashboard (Test route outside group)
Route::get('/admin/tv-eload', [TelevisionEloadController::class, 'adminDashboard'])
    ->middleware(['web', 'auth', 'role:admin'])
    ->name('admin.tv-eload');

// Admin API Routes
Route::middleware(['web', 'auth', 'role:admin,super_admin'])->group(function () {
    // Employee Statistics API
    Route::get('/api/admin/employee-stats', [AdminController::class, 'getEmployeeStats'])->name('api.admin.employee-stats');
});

// Cashier API Routes
Route::middleware(['web', 'auth', 'role:cashier,admin,super_admin'])->group(function () {
    // Latest Sales API
    Route::get('/api/cashier/sales/latest', [SalesController::class, 'getLatestSales'])->name('api.cashier.sales.latest');
});

// Television E-Load Routes (Admin and Super Admin)
Route::middleware(['web', 'auth', 'role:admin,super_admin'])->group(function () {
    // TV E-Load Dashboard
    Route::get('/television-eload', [TelevisionEloadController::class, 'dashboard'])->name('television-eload.dashboard');

    // ... (rest of the code remains the same)
    Route::get('/television-eload/stats', [TelevisionEloadController::class, 'getStats'])->name('television-eload.stats');

    // TV E-Load Processing

    Route::post('/television-eload/process', [TelevisionEloadController::class, 'processLoad'])->name('television-eload.process');

    Route::post('/television-eload/manual-process', [TelevisionEloadController::class, 'manualLoadProcess'])->name('television-eload.manual-process');

    Route::get('/television-eload/transactions', [TelevisionEloadController::class, 'getTransactions'])->name('television-eload.transactions');

    Route::post('/television-eload/provider-info', [TelevisionEloadController::class, 'getProviderInfo'])->name('television-eload.provider-info');

    

    // TV E-Load Search

    Route::post('/television-eload/search', [TelevisionEloadController::class, 'searchTransactions'])->name('television-eload.search');

    

    // TV E-Load Cleanup

    Route::post('/television-eload/cleanup-old', [TelevisionEloadController::class, 'cleanupOldTransactions'])->name('television-eload.cleanup-old');

    

    // TV E-Load Export

    Route::get('/television-eload/export', [TelevisionEloadController::class, 'exportTransactions'])->name('television-eload.export');

});



// Email Receipt Routes

Route::post("/sales/{sale}/send-email", [EmailReceiptController::class, "sendReceiptEmail"])->name("sales.send.email");

Route::post("/sales/{sale}/quick-send", [EmailReceiptController::class, "quickSendReceipt"])->name("sales.quick.send");

Route::get("/test-email", [EmailReceiptController::class, "testEmailConfig"])->name("test.email");