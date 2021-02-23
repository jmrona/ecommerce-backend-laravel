<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/recover', [AuthController::class, 'recoverPassword']);
Route::post('/reset', [AuthController::class, 'resetPassword']);


// Route::middleware('auth:sanctum')->get('/logout',[AuthController::class, 'logout']);
// Route::middleware('auth:sanctum')->get('/renew',[AuthController::class, 'renew']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/renew', [AuthController::class, 'renew']);

    Route::get('/users', [UserController::class, 'index']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::post('/user/{id}/avatar', [UserController::class, 'setAvatar']);
    Route::delete('/user/{id}/avatar', [UserController::class, 'deleteAvatar']);
    Route::post('/user/{id}/setBan', [UserController::class, 'setBanUser']);
    Route::post('/user/{id}/removeBan', [UserController::class, 'removeBanUser']);
});
