<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardenController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\ShiftController; // ✅ ShiftController added

// Public Route
Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');
// Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Wardens
    Route::resource('wardens', WardenController::class);

    // Incident Reports
    Route::resource('incident-reports', IncidentReportController::class);

    // Teams
    Route::resource('teams', TeamController::class);

    // Roles
    Route::resource('roles', RoleController::class);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'updateAll'])->name('settings.update');


    // Force Dispatch
    Route::post('/dispatch-squad', [DispatchController::class, 'dispatchSquad'])->name('admin.dispatch.squad');

    // Shifts ✅ (Newly Added)
    Route::resource('shifts', ShiftController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // ✅ Warden Dashboard (Test map page)
    Route::middleware(['role:employee'])->get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('warden.dashboard');

    // Show the form to create user
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');

// Handle form submission to store user
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

});

require __DIR__.'/auth.php';
