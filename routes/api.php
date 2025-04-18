<?php

use Illuminate\Http\Request;
use App\Rules\CompromisedPassword;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntegrationController;

Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::post('/webhook/eventbrite', [IntegrationController::class, 'webhook']);
    Route::get('/platforms/{platform}/search', [IntegrationController::class, 'searchEvents']);
});

Route::post('/check-password', function (Request $request) {
    $rule = new CompromisedPassword();
    return response()->json([
        'compromised' => !$rule->passes('password', $request->input('password'))
    ]);
})->middleware('throttle:6,1'); // Rate limit to 6 requests per minute