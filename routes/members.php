<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BandMembersController;

// Routes for band member updates
Route::put('/profile/{dashboardType}/band-members-update/{user}', [BandMembersController::class, 'updateMembers'])
    ->name('band.members.update')
    ->middleware(['auth', 'verified']);
