<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\VenueApiController;
use App\Http\Controllers\APIRequestsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/venues/{id}', [VenueApiController::class, 'show']);
Route::get('/dashboard/{$dashboardType}/finances', [FinanceController::class, 'getFinanceData']);
Route::get('/bands/search', [APIRequestsController::class, 'searchBands']);
Route::post('/bands/create', [APIRequestsController::class, 'createBand']);
Route::get('/promoters/search', [APIRequestsController::class, 'searchPromoters']);
Route::post('/promoters/create', [APIRequestsController::class, 'createPromoter']);
