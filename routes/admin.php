<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\PromoterController;
use App\Http\Controllers\OtherServiceController;

Route::middleware(['auth', 'role:administrator'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Users Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    });

    // Venues Management
    Route::prefix('venues')->group(function () {
        Route::get('/', [AdminController::class, 'venues'])->name('admin.venues');
        Route::get('/create', [AdminController::class, 'createVenue'])->name('admin.venues.create');
        Route::post('/', [AdminController::class, 'storeVenue'])->name('admin.venues.store');
        Route::get('/{venue}/edit', [AdminController::class, 'editVenue'])->name('admin.venues.edit');
        Route::put('/{venue}', [AdminController::class, 'updateVenue'])->name('admin.venues.update');
        Route::delete('/{venue}', [AdminController::class, 'destroyVenue'])->name('admin.venues.destroy');
    });

    // Promoters Management
    Route::prefix('promoters')->group(function () {
        Route::get('/', [AdminController::class, 'promoters'])->name('admin.promoters');
        Route::get('/create', [AdminController::class, 'createPromoter'])->name('admin.promoters.create');
        Route::post('/', [AdminController::class, 'storePromoter'])->name('admin.promoters.store');
        Route::get('/{promoter}/edit', [AdminController::class, 'editPromoter'])->name('admin.promoters.edit');
        Route::put('/{promoter}', [AdminController::class, 'updatePromoter'])->name('admin.promoters.update');
        Route::delete('/{promoter}', [AdminController::class, 'destroyPromoter'])->name('admin.promoters.destroy');
    });

    // Services Management
    Route::prefix('services')->group(function () {
        Route::get('/', [AdminController::class, 'services'])->name('admin.services');
        Route::get('/create', [AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::get('/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
        Route::put('/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
        Route::delete('/{service}', [AdminController::class, 'destroyService'])->name('admin.services.destroy');
    });
});
