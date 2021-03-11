<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductGalleryController;
use App\Http\Controllers\UserController;

// Token no required
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/recover', [AuthController::class, 'recoverPassword']);
Route::post('/reset', [AuthController::class, 'resetPassword']);

//Token required
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/renew', [AuthController::class, 'renew']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::post('/user/{id}/avatar', [UserController::class, 'setAvatar']);
    Route::delete('/user/{id}/avatar', [UserController::class, 'deleteAvatar']);
    Route::post('/user/{id}/setBan', [UserController::class, 'setBanUser']);
    Route::post('/user/{id}/removeBan', [UserController::class, 'removeBanUser']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    // ProductGallery
    Route::delete('/picture/{id}', [ProductGalleryController::class, 'deletePicture']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

});
