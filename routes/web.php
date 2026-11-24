<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;

Route::get('/', [HomeController::class, 'index']);
// The old "Category Explorer" page is gone: categories open the pickup page directly (category chips + items).
// Public blog ("Know your scrap"): readable by everyone and indexed by search engines
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+')->name('blog.show');
Route::get('/learn/{id}', [\App\Http\Controllers\BlogController::class, 'legacy'])->whereNumber('id')->name('learn.show'); // old links -> 301 to /blog/<slug>
Route::get('/sitemap.xml', [\App\Http\Controllers\BlogController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\BlogController::class, 'robots'])->name('robots');
Route::get('/explore-categories', fn () => redirect()->route('customer.orders.create'))->name('categories.explore');
Route::get('/category/{id}', fn ($id) => redirect()->route('customer.orders.create', ['category' => $id]))->name('category.show');

Route::get('/admin/login', function () {
    if (session('is_admin')) {
        return redirect('/dashboard');
    }

    return view('login');
});

Route::post('/admin/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('admin.logout');

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminUserController;

Route::middleware('admin')->group(function () {
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
Route::get('/admin/users/{uuid}', [AdminUserController::class, 'show'])->name('admin.users.show');
});

// Guests can browse the pickup page; logging in is asked only when they submit the request.
Route::get('/customer/request-pickup', [OrderController::class, 'create'])->name('customer.orders.create');

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

Route::middleware(['auth', 'single.session'])->group(function () {
    // polled by every page: answers 401 + code=session_replaced when this login was replaced elsewhere
    Route::get('/customer/session-check', fn () => response()->json(['active' => true]))->name('customer.session.check');

    Route::get('/customer/home', function () {
        return view('customer.home');
    })->name('customer.home');

    Route::get('/customer/categories', [\App\Http\Controllers\HomeController::class, 'categories'])->name('customer.categories');

    Route::get('/customer/support', [\App\Http\Controllers\SupportController::class, 'supportPage'])->name('customer.support');
    Route::post('/customer/support/ticket', [\App\Http\Controllers\SupportController::class, 'webCreateTicket'])->name('customer.support.ticket');
    Route::get('/customer/refer', [\App\Http\Controllers\SupportController::class, 'referPage'])->name('customer.refer');

    Route::get('/customer/rewards', function () {
        return view('customer.rewards');
    })->name('customer.rewards');

    Route::get('/customer/profile', function () {
        return view('customer.profile');
    })->name('customer.profile');

    Route::post('/customer/profile/update', [\App\Http\Controllers\CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/customer/profile/phone/send-otp', [\App\Http\Controllers\CustomerAuthController::class, 'requestPhoneChange'])->name('customer.profile.phone.send');
    Route::post('/customer/profile/phone/verify', [\App\Http\Controllers\CustomerAuthController::class, 'confirmPhoneChange'])->name('customer.profile.phone.verify');

    Route::get('/customer/payments', function () {
        return view('customer.payments');
    })->name('customer.payments');

    Route::get('/customer/faqs', [\App\Http\Controllers\HomeController::class, 'faqs'])->name('customer.faqs');

    Route::get('/customer/change-password', function () {
        return view('customer.change-password');
    })->name('customer.change-password');

    // User Orders & Feedback
    Route::get('/customer/orders', [OrderController::class, 'index'])->name('customer.orders');
    Route::get('/customer/order/{id}', [OrderController::class, 'show'])->name('customer.order.show');
    Route::post('/customer/orders', [OrderController::class, 'store'])->name('customer.orders.store');
    Route::post('/customer/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('customer.orders.cancel');
    Route::post('/customer/feedback', [FeedbackController::class, 'store'])->name('customer.feedback.store');

    // Scrap Vehicles Web Routes
    Route::get('/customer/scrap-vehicles', function () {
        return view('customer.scrap_vehicles');
    })->name('customer.scrap_vehicles');
    Route::post('/customer/scrap-vehicles', [\App\Http\Controllers\ScrapVehicleController::class, 'store'])->name('customer.scrap_vehicles.store');

    // Payments
    Route::get('/customer/payment/{orderId}', [PaymentController::class, 'initiatePayment'])->name('customer.payment.initiate');
    Route::post('/customer/payment/{orderId}/apply-rewards', [PaymentController::class, 'applyRewards'])->name('customer.payment.apply-rewards');
    Route::post('/customer/payment/callback', [PaymentController::class, 'paymentCallback'])->name('customer.payment.callback');
    Route::get('/customer/payment/{orderId}/status', [PaymentController::class, 'status'])->whereNumber('orderId')->name('customer.payment.status');
    Route::get('/customer/payment/{orderId}/receipt', [\App\Http\Controllers\ReceiptController::class, 'customer'])->whereNumber('orderId')->name('customer.payment.receipt');
    // JSON endpoints used by the payment page (same logic as the mobile API)
    Route::post('/customer/payment/gateway-order', [\App\Http\Controllers\PaymentApiController::class, 'gatewayOrder'])->name('customer.payment.gateway-order');
    Route::post('/customer/payment/direct', [\App\Http\Controllers\PaymentApiController::class, 'payDirect'])->name('customer.payment.direct');
});

// Static Pages
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

Route::middleware('admin')->group(function () {
// "Service not available in my area" requests + admin notification feed
Route::get('/admin/service-requests', [\App\Http\Controllers\ServiceRequestController::class, 'adminIndex'])->name('admin.service-requests.index');
Route::post('/admin/service-requests/{id}/status', [\App\Http\Controllers\ServiceRequestController::class, 'adminUpdateStatus'])->name('admin.service-requests.status');
Route::delete('/admin/service-requests/{id}', [\App\Http\Controllers\ServiceRequestController::class, 'adminDestroy'])->name('admin.service-requests.destroy');
Route::get('/admin/notifications', [\App\Http\Controllers\ServiceRequestController::class, 'adminNotifications'])->name('admin.notifications');

// Site settings (address, contact, social links)
Route::get('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.index');
Route::post('/admin/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('admin.settings.update');

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

Route::get('/admin/faqs', function () {
    return view('admin.faqs');
});

Route::get('/admin/rewards', function () {
    return view('admin.rewards');
});

// Admin Orders, Feedback & Pages
Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

// Order detail page + payment actions
Route::get('/admin/orders/{id}', [App\Http\Controllers\AdminOrderController::class, 'show'])->whereNumber('id')->name('admin.orders.show');
Route::get('/admin/orders/{id}/receipt', [App\Http\Controllers\ReceiptController::class, 'admin'])->whereNumber('id')->name('admin.orders.receipt');
Route::post('/admin/orders/{id}/record-payment', [App\Http\Controllers\AdminOrderController::class, 'recordPayment'])->whereNumber('id')->name('admin.orders.record-payment');
Route::post('/admin/order-payments/{id}/review', [App\Http\Controllers\AdminOrderController::class, 'reviewPayment'])->whereNumber('id')->name('admin.order-payments.review');
Route::get('/admin/scrap-vehicles/{id}', [App\Http\Controllers\ScrapVehicleController::class, 'adminShow'])->whereNumber('id')->name('admin.scrap-vehicles.show');

// Payment settings (advance %, UPI / bank / cash / online)
Route::get('/admin/payment-settings', [App\Http\Controllers\AdminPaymentSettingsController::class, 'index'])->name('admin.payment-settings.index');
Route::post('/admin/payment-settings', [App\Http\Controllers\AdminPaymentSettingsController::class, 'update'])->name('admin.payment-settings.update');

Route::get('/admin/tickets', [App\Http\Controllers\AdminTicketController::class, 'index'])->name('admin.tickets.index');
Route::post('/admin/tickets/{id}', [App\Http\Controllers\AdminTicketController::class, 'update'])->name('admin.tickets.update');

// Home screen content (section texts, trending items) + bulk enquiry form builder / inbox
Route::get('/admin/home-content', [App\Http\Controllers\AdminHomeContentController::class, 'index'])->name('admin.home-content.index');
Route::post('/admin/home-content/sections/{key}', [App\Http\Controllers\AdminHomeContentController::class, 'updateSection'])->name('admin.home-content.section');
Route::post('/admin/home-content/trending', [App\Http\Controllers\AdminHomeContentController::class, 'saveTrending'])->name('admin.home-content.trending');
Route::get('/admin/home-content/banners/{section}', [App\Http\Controllers\AdminHomeBannerController::class, 'index'])->name('admin.home-banners.index');
Route::post('/admin/home-content/banners/{section}', [App\Http\Controllers\AdminHomeBannerController::class, 'store'])->name('admin.home-banners.store');
Route::post('/admin/home-banners/{id}', [App\Http\Controllers\AdminHomeBannerController::class, 'update'])->whereNumber('id')->name('admin.home-banners.update');
Route::delete('/admin/home-banners/{id}', [App\Http\Controllers\AdminHomeBannerController::class, 'destroy'])->whereNumber('id')->name('admin.home-banners.destroy');
Route::get('/admin/scrap-articles', [App\Http\Controllers\AdminScrapArticleController::class, 'index'])->name('admin.scrap-articles.index');
Route::get('/admin/scrap-articles/create', [App\Http\Controllers\AdminScrapArticleController::class, 'create'])->name('admin.scrap-articles.create');
Route::post('/admin/scrap-articles', [App\Http\Controllers\AdminScrapArticleController::class, 'store'])->name('admin.scrap-articles.store');
Route::get('/admin/scrap-articles/{id}/edit', [App\Http\Controllers\AdminScrapArticleController::class, 'edit'])->whereNumber('id')->name('admin.scrap-articles.edit');
Route::post('/admin/scrap-articles/{id}', [App\Http\Controllers\AdminScrapArticleController::class, 'update'])->whereNumber('id')->name('admin.scrap-articles.update');
Route::delete('/admin/scrap-articles/{id}', [App\Http\Controllers\AdminScrapArticleController::class, 'destroy'])->whereNumber('id')->name('admin.scrap-articles.destroy');
Route::get('/admin/bulk-enquiries', [App\Http\Controllers\AdminBulkEnquiryController::class, 'index'])->name('admin.bulk-enquiries.index');
Route::post('/admin/bulk-enquiries/fields', [App\Http\Controllers\AdminBulkEnquiryController::class, 'storeField'])->name('admin.bulk-enquiries.fields.store');
Route::post('/admin/bulk-enquiries/fields/{id}', [App\Http\Controllers\AdminBulkEnquiryController::class, 'updateField'])->name('admin.bulk-enquiries.fields.update');
Route::delete('/admin/bulk-enquiries/fields/{id}', [App\Http\Controllers\AdminBulkEnquiryController::class, 'destroyField'])->name('admin.bulk-enquiries.fields.destroy');
Route::post('/admin/bulk-enquiries/{id}', [App\Http\Controllers\AdminBulkEnquiryController::class, 'updateEnquiry'])->whereNumber('id')->name('admin.bulk-enquiries.update');

Route::get('/admin/pincodes', [App\Http\Controllers\AdminPincodeController::class, 'index'])->name('admin.pincodes.index');
Route::post('/admin/pincodes', [App\Http\Controllers\AdminPincodeController::class, 'store'])->name('admin.pincodes.store');
Route::post('/admin/pincodes/{id}/toggle', [App\Http\Controllers\AdminPincodeController::class, 'toggle'])->name('admin.pincodes.toggle');
Route::delete('/admin/pincodes/{id}', [App\Http\Controllers\AdminPincodeController::class, 'destroy'])->name('admin.pincodes.destroy');

Route::get('/admin/scrap-vehicles',[App\Http\Controllers\ScrapVehicleController::class, 'adminIndex'])->name('admin.scrap-vehicles.index');

Route::get('/admin/feedbacks', [FeedbackController::class, 'adminIndex'])->name('admin.feedbacks.index');
Route::post('/admin/feedbacks/{id}/toggle', [FeedbackController::class, 'toggleApproval'])->name('admin.feedbacks.toggle');

Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages.index');
Route::get('/admin/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
Route::post('/admin/pages/store', [PageController::class, 'store'])->name('admin.pages.store');
Route::get('/admin/pages/{id}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
Route::post('/admin/pages/{id}', [PageController::class, 'update'])->name('admin.pages.update');
Route::delete('/admin/pages/{id}', [PageController::class, 'destroy'])->name('admin.pages.destroy');
});

// Categories AJAX API endpoints
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\RewardController;

Route::prefix('api')->middleware('admin')->group(function () {
    Route::post('categories/{id}', [CategoryController::class, 'update']);
    Route::apiResource('categories', CategoryController::class)->except(['update']);

    Route::post('subcategories/{id}', [SubcategoryController::class, 'update']);
    Route::apiResource('subcategories', SubcategoryController::class)->except(['update']);

    Route::post('banners/{id}', [BannerController::class, 'update']);
    Route::post('banners/{id}/status', [BannerController::class, 'toggleStatus']);
    Route::apiResource('banners', BannerController::class)->except(['update']);

    Route::post('faqs/{id}', [FaqController::class, 'update']);
    Route::post('faqs/{id}/status', [FaqController::class, 'toggleStatus']);
    Route::apiResource('faqs', FaqController::class)->except(['update']);

    Route::get('rewards/settings', [RewardController::class, 'getSettings']);
    Route::post('rewards/settings', [RewardController::class, 'updateSettings']);
    Route::post('rewards/{id}', [RewardController::class, 'update']);
    Route::post('rewards/{id}/status', [RewardController::class, 'toggleStatus']);
    Route::apiResource('rewards', RewardController::class)->except(['update']);
});
