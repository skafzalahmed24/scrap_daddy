<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/explore-categories', [HomeController::class, 'categories'])->name('categories.explore');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');

Route::get('/admin/login', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/customer/login', function () {
    return view('customer-login');
});

Route::get('/customer/register', function () {
    return view('customer-register');
});

Route::get('/customer/verify-otp', function () {
    return view('customer-verify-otp');
});

Route::get('/customer/forgot-password', function () {
    return view('customer-forgot-password');
});

Route::get('/customer/reset-password', function () {
    return view('customer-reset-password');
});

Route::get('/customer/logout-web', function () {
    auth()->guard('web')->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/customer/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/customer/home', function () {
        return view('customer.home');
    })->name('customer.home');

    Route::get('/customer/rewards', function () {
        return view('customer.rewards');
    })->name('customer.rewards');

    Route::get('/customer/profile', function () {
        return view('customer.profile');
    })->name('customer.profile');

    Route::post('/customer/profile/update', [\App\Http\Controllers\CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');

    Route::get('/customer/payments', function () {
        return view('customer.payments');
    })->name('customer.payments');

    Route::get('/customer/change-password', function () {
        return view('customer.change-password');
    })->name('customer.change-password');

    // User Orders & Feedback
    Route::get('/customer/orders', [OrderController::class, 'index'])->name('customer.orders');
    Route::get('/customer/order/{id}', [OrderController::class, 'show'])->name('customer.order.show');
    Route::get('/customer/request-pickup', [OrderController::class, 'create'])->name('customer.orders.create');
    Route::post('/customer/orders', [OrderController::class, 'store'])->name('customer.orders.store');
    Route::post('/customer/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('customer.orders.cancel');
    Route::post('/customer/feedback', [FeedbackController::class, 'store'])->name('customer.feedback.store');

    // Payments
    Route::get('/customer/payment/{orderId}', [PaymentController::class, 'initiatePayment'])->name('customer.payment.initiate');
    Route::post('/customer/payment/callback', [PaymentController::class, 'paymentCallback'])->name('customer.payment.callback');
});

// Static Pages
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Categories Admin View
Route::get('/admin/categories', function () {
    return view('admin.categories');
});

Route::get('/admin/subcategories', function () {
    return view('admin.subcategories');
});

Route::get('/admin/banners', function () {
    return view('admin.banners');
});

// Admin Orders, Feedback & Pages
Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

Route::get('/admin/feedbacks', [FeedbackController::class, 'adminIndex'])->name('admin.feedbacks.index');
Route::post('/admin/feedbacks/{id}/toggle', [FeedbackController::class, 'toggleApproval'])->name('admin.feedbacks.toggle');

Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages.index');
Route::get('/admin/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
Route::post('/admin/pages/store', [PageController::class, 'store'])->name('admin.pages.store');
Route::get('/admin/pages/{id}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
Route::post('/admin/pages/{id}', [PageController::class, 'update'])->name('admin.pages.update');
Route::delete('/admin/pages/{id}', [PageController::class, 'destroy'])->name('admin.pages.destroy');

// Categories AJAX API endpoints
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\BannerController;

Route::prefix('api')->group(function () {
    Route::post('categories/{id}', [CategoryController::class, 'update']);
    Route::apiResource('categories', CategoryController::class)->except(['update']);

    Route::post('subcategories/{id}', [SubcategoryController::class, 'update']);
    Route::apiResource('subcategories', SubcategoryController::class)->except(['update']);

    Route::post('banners/{id}', [BannerController::class, 'update']);
    Route::apiResource('banners', BannerController::class)->except(['update']);
});
