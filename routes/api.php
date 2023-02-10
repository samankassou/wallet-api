<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\V1\AuthController;
use App\Http\Controllers\api\V1\UserController;
use App\Http\Controllers\Api\V1\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')
    ->group(function () {

        /* Auth routes */
        Route::prefix('auth')->middleware('guest')->group(function () {
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
        });

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/user', function (Request $request) {
                return $request->user();
            });
            Route::apiResource('users', UserController::class);
            Route::apiResource('categories', CategoryController::class);
        });
    });
