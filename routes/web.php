<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MyBriefController;
use App\Http\Controllers\Web\MyPlanController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Web\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Web\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Web\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Web\Admin\CmsController as AdminCmsController;
use App\Http\Controllers\Web\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Web\Admin\WhatsAppTemplateController as AdminWhatsAppTemplateController;
use App\Http\Controllers\Web\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Web\Admin\FulfilmentController as AdminFulfilmentController;
use App\Http\Controllers\Web\Admin\WhatsAppDashboardController;
use App\Http\Controllers\Web\Admin\ResetOperationsController;


Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/shop', [ShopController::class,'index'])->name('shop');
Route::get('/how-it-works', [HomeController::class,'howItWorks'])->name('how-it-works');
Route::view('/our-standards', 'our-standards')->name('our-standards');
Route::view('/learn', 'learn')->name('learn');
Route::view('/support', 'support')->name('support');
Route::get('/product/{product:slug}', [ShopController::class,'show'])->name('product.show');
Route::get('/mybrief', [MyBriefController::class, 'show'])->middleware('auth')->name('my-brief');
Route::get('/my-plan', [MyPlanController::class, 'show'])->middleware('auth')->name('my-plan');
Route::post('/my-plan', [MyPlanController::class, 'store'])->middleware('auth')->name('my-plan.store');
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->middleware('guest')->name('login.post');
Route::get('/register', [AuthController::class,'showRegister'])->name('register');
Route::post('/register', [AuthController::class,'register'])->middleware('guest')->name('register.post');
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function() {
    Route::get('/cart', [CartController::class,'index'])->name('cart');
    Route::get('/checkout', [CheckoutController::class,'show'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class,'store'])->name('checkout.store');
    Route::get('/pay/{order}', [PaymentController::class,'show'])->name('payment.show');
    Route::get('/payment-success/{order}', [PaymentController::class,'success'])->name('payment.success');
    Route::get('/account', [AccountController::class,'index'])->name('account');
    Route::get('/account/orders/{order}', [AccountController::class,'order'])->name('account.order');

    Route::prefix('admin')->name('admin.')->middleware('admin.permission')->group(function() {
        Route::get('/', [AdminDashboardController::class,'index'])->name('dashboard');

        Route::get('/products',[AdminProductController::class,'index'])->name('products.index');
        Route::get('/products/export',[AdminProductController::class,'export'])->name('products.export');
        Route::get('/products/create',[AdminProductController::class,'create'])->name('products.create');
        Route::post('/products',[AdminProductController::class,'store'])->name('products.store');
        Route::get('/products/{product}/edit',[AdminProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[AdminProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[AdminProductController::class,'destroy'])->name('products.destroy');

        Route::get('/orders',[AdminOrderController::class,'index'])->name('orders.index');
        Route::get('/orders/export',[AdminOrderController::class,'export'])->name('orders.export');
        Route::get('/orders/{order}',[AdminOrderController::class,'show'])->name('orders.show');
        Route::put('/orders/{order}',[AdminOrderController::class,'update'])->name('orders.update');

        Route::get('/customers',[AdminCustomerController::class,'index'])->name('customers.index');
        Route::get('/customers/export',[AdminCustomerController::class,'export'])->name('customers.export');
        Route::get('/customers/{user}',[AdminCustomerController::class,'show'])->name('customers.show');

        Route::get('/shipping',[AdminShippingController::class,'index'])->name('shipping.index');
        Route::post('/shipping',[AdminShippingController::class,'store'])->name('shipping.store');
        Route::post('/shipping/{shippingMethod}/rates',[AdminShippingController::class,'rate'])->name('shipping.rate');

        Route::get('/coupons',[AdminCouponController::class,'index'])->name('coupons.index');
        Route::post('/coupons',[AdminCouponController::class,'store'])->name('coupons.store');

        Route::get('/media',[AdminMediaController::class,'index'])->name('media.index');
        Route::post('/media',[AdminMediaController::class,'store'])->name('media.store');

        Route::get('/cms',[AdminCmsController::class,'index'])->name('cms.index');
        Route::put('/cms/{page}',[AdminCmsController::class,'update'])->name('cms.update');
        Route::get('/cms/{page}/sections',[AdminCmsController::class,'sections'])->name('cms.sections');
        Route::post('/cms/{page}/sections',[AdminCmsController::class,'storeSection'])->name('cms.sections.store');
        Route::post('/cms/{page}/sections/reorder',[AdminCmsController::class,'reorderSections'])->name('cms.sections.reorder');
        Route::put('/cms/sections/{section}',[AdminCmsController::class,'updateSection'])->name('cms.sections.update');
        Route::post('/cms/sections/{section}/duplicate',[AdminCmsController::class,'duplicateSection'])->name('cms.sections.duplicate');
        Route::delete('/cms/sections/{section}',[AdminCmsController::class,'destroySection'])->name('cms.sections.destroy');

        Route::get('/inventory',[AdminInventoryController::class,'index'])->name('inventory.index');
        Route::get('/inventory/export',[AdminInventoryController::class,'export'])->name('inventory.export');
        Route::post('/inventory/{inventory}/adjust',[AdminInventoryController::class,'adjust'])->name('inventory.adjust');
        Route::get('/whatsapp/templates',[AdminWhatsAppTemplateController::class,'index'])->name('whatsapp.templates');
        Route::get('/payments',[AdminPaymentController::class,'index'])->name('payments.index');
        Route::get('/payments/export',[AdminPaymentController::class,'export'])->name('payments.export');
        Route::get('/payments/{payment}',[AdminPaymentController::class,'show'])->name('payments.show');
        Route::post('/payments/{payment}/reconcile',[AdminPaymentController::class,'reconcile'])->name('payments.reconcile');
        Route::get('/fulfilment',[AdminFulfilmentController::class,'index'])->name('fulfilment.index');
        Route::get('/fulfilment/export',[AdminFulfilmentController::class,'export'])->name('fulfilment.export');
        Route::put('/fulfilment/{shipment}',[AdminFulfilmentController::class,'update'])->name('fulfilment.update');
        Route::post('/orders/{order}/create-shipment',[AdminFulfilmentController::class,'create'])->name('orders.create-shipment');
        Route::get('/whatsapp',[WhatsAppDashboardController::class,'index'])->name('whatsapp.dashboard');
        Route::get('/reset-operations',[ResetOperationsController::class,'index'])->name('reset.operations');
        Route::post('/whatsapp/templates',[AdminWhatsAppTemplateController::class,'store'])->name('whatsapp.templates.store');
    });
});
