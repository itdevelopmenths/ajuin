<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/submit', [PublicTicketController::class, 'form'])->name('public.submit.form');
Route::post('/submit', [PublicTicketController::class, 'store'])->middleware('throttle:10,60')->name('public.submit.store');
Route::get('/submit/{token}', function() { return redirect('/submit'); });
Route::get('/track', [PublicTicketController::class, 'search'])->name('public.track.search');
Route::get('/track/{number}', [PublicTicketController::class, 'track'])->name('public.track');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,60')->name('register.store');

    Route::get('/verify-otp', [RegisterController::class, 'showVerify'])->name('otp.verify');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->middleware('throttle:10,10')->name('otp.verify.store');
    Route::post('/verify-otp/resend', [RegisterController::class, 'resendOtp'])->middleware('throttle:5,10')->name('otp.resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:report.view')->name('reports.index');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('permission:role.manage');
        Route::resource('users', UserController::class)->only(['index', 'store', 'edit', 'update', 'destroy'])->middleware('permission:user.view|user.create|user.edit|user.delete');
        Route::get('/stores/data', [StoreController::class, 'data'])->middleware('permission:store.manage')->name('stores.data');
        Route::resource('stores', StoreController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('permission:store.manage');
        Route::get('/stores-qr', [StoreController::class, 'globalQr'])->middleware('permission:store.manage')->name('stores.global-qr');
    });
});
