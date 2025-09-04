<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
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
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AreaAssignmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AlertController;
use App\Mail\TestMail;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');

// Admin Dashboard (UPDATED with real-time support)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

// Real-time dashboard data endpoint (AJAX)
Route::get('/dashboard/realtime-data', [DashboardController::class, 'realtimeData'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard.realtime');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Admin resources with proper prefixes
    Route::prefix('admin')->name('admin.')->group(function() {
        // Wardens
        Route::resource('wardens', WardenController::class);
        Route::patch('/wardens/{warden}/toggle-status', [WardenController::class, 'toggleStatus'])->name('wardens.toggleStatus');
        Route::get('/wardens-map', [WardenController::class, 'map'])->name('wardens.map');
        
        // Area Assignments for Wardens
        Route::resource('areas', AreaAssignmentController::class);
        Route::get('/warden/{warden}/areas', [AreaAssignmentController::class, 'showWardenAreas'])->name('warden.areas');
        
        // Tasks Management
        Route::resource('tasks', TaskController::class);
        Route::patch('/tasks/{task}/mark-completed', [TaskController::class, 'markCompleted'])->name('tasks.complete');
        Route::patch('/tasks/{task}/mark-in-progress', [TaskController::class, 'markInProgress'])->name('tasks.progress');
        
        // Alerts Management
        Route::resource('alerts', AlertController::class);
        Route::patch('/alerts/{alert}/mark-read', [AlertController::class, 'markRead'])->name('alerts.read');
        Route::post('/alerts/send-to-all', [AlertController::class, 'sendToAll'])->name('alerts.sendToAll');
    });
    
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

    // Shifts
    Route::resource('shifts', ShiftController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    
    // Warden Dashboard (Test map page)
    Route::middleware(['role:employee'])->get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('warden.dashboard');

    // API Endpoints for Mobile App
    Route::prefix('api')->name('api.')->group(function () {
        // Dashboard stats for warden app
        Route::get('/warden/dashboard-stats', [DashboardController::class, 'wardenDashboardStats'])
            ->middleware('auth:sanctum')
            ->name('warden.dashboard-stats');
            
        // Warden tasks
        Route::get('/warden/tasks', [TaskController::class, 'wardenTasks'])
            ->middleware('auth:sanctum')
            ->name('warden.tasks');
            
        // Warden assigned areas
        Route::get('/warden/areas', [AreaAssignmentController::class, 'wardenAreas'])
            ->middleware('auth:sanctum')
            ->name('warden.assigned-areas');
            
        // Warden alerts
        Route::get('/warden/alerts', [AlertController::class, 'wardenAlerts'])
            ->middleware('auth:sanctum')
            ->name('warden.alerts');
    });

    // Show the form to create user
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');

    // Handle form submission to store user
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
});

// Test Mail Route (for development/testing)
Route::get('/send-test-mail', function () {
    Mail::to('idreeskhan067@gmail.com')->send(new TestMail());
    return 'Test email sent!';
});

require __DIR__.'/auth.php';