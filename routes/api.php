<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceController;
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

Route::get('/dashboard/{$dashboardType}/finances', [FinanceController::class, 'getFinanceData']);
Route::get('/bands/search', [APIRequestsController::class, 'searchBands']);
Route::post('/bands/create', [APIRequestsController::class, 'createBand']);
Route::get('/promoters/search', [APIRequestsController::class, 'searchPromoters']);
Route::post('/promoters/create', [APIRequestsController::class, 'createPromoter']);
Route::get('/venues/search', [APIRequestsController::class, 'searchVenues']);
Route::post('/venues/create', [APIRequestsController::class, 'createVenue']);
Route::post('/profile/{dashboardType}/{id}/update-api-keys', [APIRequestsController::class, 'updateAPI']);
Route::post('/profile/{dashboardType}/settings/update', [APIRequestsController::class, 'updateModule'])->name('settings.updateModule');
Route::post('/profile/{dashboardType}/communications/update', [APIRequestsController::class, 'updateCommunications'])->name('settings.updateCommunications');
