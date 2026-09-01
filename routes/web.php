<?php

use App\Http\Controllers\Admin\MaintenanceTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\TierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/track', [PublicTicketController::class, 'search'])->name('public.track.search');
Route::get('/track/{number}', [PublicTicketController::class, 'track'])->name('public.track');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::middleware('permission:ticket.view')->group(function (): void {
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/data', [TicketController::class, 'data'])->name('tickets.data');
        Route::get('/tickets/export', [TicketController::class, 'export'])->name('tickets.export');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    });

    Route::middleware('permission:ticket.create')->group(function (): void {
        Route::get('/tickets-create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    });

    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->middleware('permission:ticket.update_status')
        ->name('tickets.update-status');

    Route::post('/tickets/{ticket}/notes', [TicketController::class, 'addNote'])
        ->middleware('permission:ticket.update_status')
        ->name('tickets.notes.store');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:report.view')->name('reports.index');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::resource('roles', RoleController::class)->only(['index'])->middleware('permission:role.view|role.manage');
        Route::resource('roles', RoleController::class)->only(['store', 'update', 'destroy'])->middleware('permission:role.manage');

        Route::resource('users', UserController::class)->only(['index'])->middleware('permission:user.view|user.create|user.edit|user.delete');
        Route::resource('users', UserController::class)->only(['edit', 'update'])->middleware('permission:user.edit');
        Route::resource('users', UserController::class)->only(['store'])->middleware('permission:user.create');
        Route::resource('users', UserController::class)->only(['destroy'])->middleware('permission:user.delete');

        Route::get('/stores/data', [StoreController::class, 'data'])->middleware('permission:store.view|store.manage')->name('stores.data');
        Route::resource('stores', StoreController::class)->only(['index'])->middleware('permission:store.view|store.manage');
        Route::resource('stores', StoreController::class)->only(['store', 'update', 'destroy'])->middleware('permission:store.manage');

        Route::resource('maintenance-types', MaintenanceTypeController::class)->only(['index'])->middleware('permission:maintenance_type.view|maintenance_type.manage');
        Route::resource('maintenance-types', MaintenanceTypeController::class)->only(['store', 'update', 'destroy'])->middleware('permission:maintenance_type.manage');
        Route::resource('tiers', TierController::class)->only(['store', 'update', 'destroy'])->middleware('permission:maintenance_type.manage');
    });
});
