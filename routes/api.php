<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntegrationController;

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('/webhook/eventbrite', [IntegrationController::class, 'webhook']);
    Route::get('/platforms/{platform}/search', [IntegrationController::class, 'searchEvents']);
});
