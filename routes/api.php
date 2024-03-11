<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MoniController;
use App\Http\Controllers\CellarController;
use App\Http\Controllers\SensorController;
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
    Route::post('/signIn', [UserController::class, 'store']); // registration of new user
    Route::post('/login', [UserController::class, 'Login']); // user login
    Route::put('/updatePass', [UserController::class, 'updatePassword']); // update the password
    Route::post('/checkLogged', [UserController::class,'checkLogged']);
});

Route::group(['prefix' => 'Monitoring'], function () {
    Route::post('/report',[MoniController::class, 'store']);
    Route::post('/upload',  [ImageController::class, 'store']);
});
Route::group(['prefix' => 'Sensor'], function () {
    Route::post('/new',[SensorController::class, 'store']);
    Route::put('/update',[SensorController::class, 'update']);
});

Route::group(['prefix' => 'Cellars'], function () {
    Route::post('/new',[CellarController::class, 'store']);
    Route::post('/retrive',  [CellarController::class, 'retrive_cellars']);
    Route::post('/sensors',  [CellarController::class, 'CellarsMoniINFO']);
    Route::post('/assCellar',  [CellarController::class, 'AssCellar']);

    //anche se è un metodo che restituisce informazioni dal database
    //, utilizzo il metodo post per nascondere il token (che altimenti sarebbe nel'url)
});



