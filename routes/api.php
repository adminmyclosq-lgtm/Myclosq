<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ResetController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RazorpayWebhookController;
use App\Http\Controllers\Api\ShipmentTrackingController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\CmsController as AdminCmsController;
use App\Http\Controllers\Api\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Api\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Api\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\Phase2DDashboardController;

Route::prefix('v1')->group(function() {
    Route::get('/home',[HomeController::class,'index']);
    Route::get('/products',[ProductController::class,'index']);
    Route::get('/products/{product}',[ProductController::class,'show']);

    Route::post('/auth/register',[AuthController::class,'register']);
    Route::post('/auth/login',[AuthController::class,'login']);

    Route::get('/webhooks/whatsapp',[WhatsAppWebhookController::class,'verify']);
    Route::post('/webhooks/whatsapp',[WhatsAppWebhookController::class,'receive']);
    Route::post('/webhooks/razorpay',[RazorpayWebhookController::class,'receive']);
    Route::post('/webhooks/shipment',[ShipmentTrackingController::class,'receive']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/me',[AuthController::class,'me']);
        Route::post('/auth/logout',[AuthController::class,'logout']);

        Route::get('/cart',[CartController::class,'show']);
        Route::post('/cart/items',[CartController::class,'store']);
        Route::patch('/cart/items/{cartItem}',[CartController::class,'update']);
        Route::delete('/cart/items/{cartItem}',[CartController::class,'destroy']);

        Route::post('/shipping/options',[ShippingController::class,'options']);
        Route::post('/coupons/apply',[CouponController::class,'apply']);

        Route::get('/orders',[OrderController::class,'index']);
        Route::post('/orders',[OrderController::class,'store']);
        Route::get('/orders/{order}',[OrderController::class,'show']);
        Route::post('/orders/{order}/payment',[PaymentController::class,'create']);
        Route::post('/orders/{order}/payment/verify',[PaymentController::class,'verify']);

        Route::get('/reset/profile',[ResetController::class,'show']);
        Route::post('/reset/checkins',[ResetController::class,'checkin']);
        Route::get('/dashboard',[DashboardController::class,'index']);

        Route::prefix('admin')->middleware('admin.permission')->group(function() {
            Route::get('/dashboard',[AdminDashboardController::class,'index']);
            Route::get('/products',[AdminProductController::class,'index']);
            Route::post('/products',[AdminProductController::class,'store']);
            Route::put('/products/{product}',[AdminProductController::class,'update']);
            Route::get('/orders',[AdminOrderController::class,'index']);
            Route::put('/orders/{order}',[AdminOrderController::class,'update']);
            Route::get('/customers',[AdminCustomerController::class,'index']);
            Route::get('/customers/{user}',[AdminCustomerController::class,'show']);
            Route::get('/shipping',[AdminShippingController::class,'index']);
            Route::post('/shipping',[AdminShippingController::class,'store']);
            Route::post('/shipping/{shippingMethod}/rates',[AdminShippingController::class,'rate']);
            Route::get('/coupons',[AdminCouponController::class,'index']);
            Route::post('/coupons',[AdminCouponController::class,'store']);
            Route::get('/inventory',[AdminInventoryController::class,'index']);
            Route::get('/payments',[AdminPaymentController::class,'index']);
            Route::get('/payments/{payment}',[AdminPaymentController::class,'show']);
            Route::post('/payments/{payment}/reconcile',[AdminPaymentController::class,'reconcile']);
            Route::get('/phase2d/payments',[Phase2DDashboardController::class,'payments']);
            Route::get('/phase2d/fulfilment',[Phase2DDashboardController::class,'fulfilment']);
            Route::get('/phase2d/whatsapp',[Phase2DDashboardController::class,'whatsapp']);
            Route::get('/phase2d/reset',[Phase2DDashboardController::class,'reset']);
            Route::post('/inventory/{inventory}/adjust',[AdminInventoryController::class,'adjust']);
            Route::get('/cms/pages',[AdminCmsController::class,'index']);
            Route::put('/cms/pages/{cmsPage}',[AdminCmsController::class,'update']);
        });
    });
});
