<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MoniController;
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
/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::group(['prefix' => 'User'], function () {
    Route::post('/Sign-in', [UserController::class, 'store']);//registrazione nuovo utente
    Route::post('/Login', [UserController::class, 'Login']);//login utente
});

Route::group(['prefix' => 'Monitoring'], function () {
    Route::post('/report',[MoniController::class, 'store']);
    Route::post('/upload',  [ImageController::class, 'store']);
});



