<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LiveSummaryController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/live/summary', LiveSummaryController::class)->name('live.summary');
    Route::get('/calendar', CalendarController::class)->name('calendar');

    Route::get('/account/members', [AccountController::class, 'members'])->name('account.members');
    Route::post('/account/members', [AccountController::class, 'addMember'])->name('account.members.store');
    Route::delete('/account/members/{user}', [AccountController::class, 'removeMember'])->name('account.members.destroy');
    Route::post('/account/switch', [AccountController::class, 'switchAccount'])->name('account.switch');
    Route::get('/account/activity', ActivityLogController::class)->name('account.activity');

    Route::resource('transactions', TransactionController::class)->except('show');
    Route::resource('wallets', WalletController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])
        ->name('push-subscriptions.store');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->name('push-subscriptions.destroy');
});

Route::get('/404', fn () => abort(404))->name('404');
