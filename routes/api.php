<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScrapVehicleController;

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

// Admin Login Route
Route::post('/login', [AuthController::class, 'login']);

// Customer App APIs
Route::prefix('customer')->group(function () {
    // Public Auth Routes
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp']);
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);
    // Public Data Routes
    Route::post('/home', [CustomerAuthController::class, 'home']);
    Route::post('/banners', [CustomerAuthController::class, 'banners']);
    Route::post('/categories', [CustomerAuthController::class, 'categories']);
    Route::post('/subcategories', [CustomerAuthController::class, 'subcategories']);
    Route::post('/check-pincode', [CustomerAuthController::class, 'checkPincode']);
    Route::post('/service-request', [\App\Http\Controllers\ServiceRequestController::class, 'store']);
    Route::post('/pages',[CustomerAuthController::class, 'page']);
    Route::post('/support-info', [\App\Http\Controllers\SupportController::class, 'info']);
    Route::post('/faqs', [CustomerAuthController::class, 'faqs']);
    Route::post('/upload-images', [UploadController::class, 'uploadImages']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Lightweight check the app polls so a replaced login is noticed on the spot
        Route::post('/session-check', fn () => response()->json(['status' => 1, 'message' => 'Session active']));

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
        Route::post('/orders/verify-payment', [CustomerAuthController::class, 'verifyPayment']);
        Route::post('/orders/payment-options', [\App\Http\Controllers\PaymentApiController::class, 'options']);
        Route::post('/orders/gateway-order', [\App\Http\Controllers\PaymentApiController::class, 'gatewayOrder']);
        Route::post('/orders/pay-direct', [\App\Http\Controllers\PaymentApiController::class, 'payDirect']);
        Route::post('/orders/redeem-coins', [CustomerAuthController::class, 'redeemCoins']);
        Route::post('/orders/collect-coins', [CustomerAuthController::class, 'collectCoins']);
        
        // Rewards
        Route::post('/rewards/info', [CustomerAuthController::class, 'getRewardSettings']);

        // Refer a friend + help & support tickets
        Route::post('/referral', [\App\Http\Controllers\SupportController::class, 'referral']);
        Route::post('/tickets/create', [\App\Http\Controllers\SupportController::class, 'createTicket']);
        Route::post('/tickets', [\App\Http\Controllers\SupportController::class, 'myTickets']);

        // Scrap Vehicles
        Route::post('/scrap-vehicles', [ScrapVehicleController::class, 'store']);
        Route::post('/scrap-vehicles/list', [ScrapVehicleController::class, 'customerIndex']);
    });
});

// Admin APIs
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::post('/scrap-vehicles', [ScrapVehicleController::class, 'index']);
    Route::post('/scrap-vehicles/status', [ScrapVehicleController::class, 'updateStatus']);
});
