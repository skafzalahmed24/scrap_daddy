<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\UploadController;

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

// Customer App APIs
Route::prefix('customer')->group(function () {
    // Public Auth Routes
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp']);
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);
    
    // Public Data Routes
    Route::post('/categories', [CustomerAuthController::class, 'categories']);
    Route::post('/subcategories', [CustomerAuthController::class, 'subcategories']);
    Route::post('/pages', [CustomerAuthController::class, 'page']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Profile
        Route::post('/user', [CustomerAuthController::class, 'user']);
        Route::post('/update-profile', [CustomerAuthController::class, 'updateProfile']);
        Route::post('/change-password', [CustomerAuthController::class, 'changePassword']);
        Route::post('/refresh-token', [CustomerAuthController::class, 'refreshToken']);
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::delete('/delete-account', [CustomerAuthController::class, 'deleteAccount']);

        // Orders & Payments
        Route::post('/orders/create', [CustomerAuthController::class, 'createOrder']);
        Route::post('/orders', [CustomerAuthController::class, 'orders']);
        Route::post('/order-details', [CustomerAuthController::class, 'showOrder']);
        Route::post('/payments', [CustomerAuthController::class, 'payments']);
    });
});


// General
Route::post('/upload-images', [UploadController::class, 'uploadImages']);
