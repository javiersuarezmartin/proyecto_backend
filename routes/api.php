<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DateController;
use App\Http\Controllers\ConfigController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group([
    'prefix' => 'auth'
], function() {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'getUserData']);    
    });
});

// Rutas de Citas

Route::group([
    'prefix' => 'date'
], function() { 

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::post('add', [DateController::class, 'store']);
        Route::get('reserved/{date}', [DateController::class, 'getReservedHours']);
        Route::get('alldates', [DateController::class, 'getAllDates']);
        Route::get('datesday/{date}', [DateController::class, 'getDatesDay']);

        Route::get('{id}', [DateController::class, 'show']);
        Route::delete('delete/{id}', [DateController::class, 'destroy']);    
    });
});

// Rutas de Config

Route::group([
    'prefix' => 'config'
], function() { 
    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::post('setconfig', [ConfigController::class, 'store']); 
        Route::get('getconfig', [ConfigController::class, 'getConfig']);
        Route::put('updateconfig', [ConfigController::class, 'updateConfig']);
    });
});
  

