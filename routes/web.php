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
use App\Http\Controllers\Web\Admin\AddressAnalyticsController;
use App\Http\Controllers\Web\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Web\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Web\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Web\ResetJourneyController;


Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/shop', [ShopController::class,'index'])->name('shop');
Route::get('/how-it-works', [HomeController::class,'howItWorks'])->name('how-it-works');
Route::view('/our-standards', 'our-standards')->name('our-standards');
Route::view('/learn', 'learn')->name('learn');
Route::view('/support', 'support')->name('support');
Route::view('/privacy-policy', 'legal.privacy')->name('privacy-policy');
Route::view('/refund-policy', 'legal.refund')->name('refund-policy');
Route::view('/shipping-policy', 'legal.shipping')->name('shipping-policy');
Route::view('/terms-of-service', 'legal.terms')->name('terms-of-service');
Route::get('/product/{product:slug}', [ShopController::class,'show'])->name('product.show');
Route::get('/mybrief', [MyBriefController::class, 'show'])->middleware('auth')->name('my-brief');
Route::get('/my-plan', [MyPlanController::class, 'show'])->middleware('auth')->name('my-plan');
Route::post('/my-plan', [MyPlanController::class, 'store'])->middleware('auth')->name('my-plan.store');
Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->middleware('guest')->name('login.post');
Route::get('/register', [AuthController::class,'showRegister'])->name('register');
Route::post('/register', [AuthController::class,'register'])->middleware('guest')->name('register.post');
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::get('/reset/activate/{qrCode:code}', [ResetJourneyController::class, 'activateFromQr'])->name('reset.activate');

Route::middleware('auth')->group(function() {
    Route::get('/reset', [ResetJourneyController::class, 'home'])->name('reset.home');
    Route::get('/reset/day0', [ResetJourneyController::class, 'day0'])->name('reset.day0');
    Route::post('/reset/day0', [ResetJourneyController::class, 'storeDay0'])->name('reset.day0.store');
    Route::get('/reset/day/{day}', [ResetJourneyController::class, 'day'])->whereNumber('day')->name('reset.day');
    Route::post('/reset/day/{day}', [ResetJourneyController::class, 'storeDay'])->whereNumber('day')->name('reset.day.store');
    Route::post('/reset/day/{day}/unusual', [ResetJourneyController::class, 'unusual'])->whereNumber('day')->name('reset.day.unusual');
    Route::post('/reset/day/{day}/positive', [ResetJourneyController::class, 'positive'])->whereNumber('day')->name('reset.day.positive');
    Route::get('/reset/report/{cycle?}', [ResetJourneyController::class, 'report'])->whereNumber('cycle')->name('reset.report');
    Route::get('/report', [ResetJourneyController::class, 'report'])->name('report');
    Route::get('/reset/testimonial', [ResetJourneyController::class, 'testimonial'])->name('reset.testimonial');
    Route::post('/reset/testimonial', [ResetJourneyController::class, 'storeTestimonial'])->name('reset.testimonial.store');
    Route::get('/reset/re-entry', [ResetJourneyController::class, 'reentry'])->name('reset.reentry');
    Route::post('/reset/re-entry', [ResetJourneyController::class, 'storeReentry'])->name('reset.reentry.store');

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
        Route::post('/products/bulk-action',[AdminProductController::class,'bulkAction'])->name('products.bulk-action');
        Route::get('/products/{product}',[AdminProductController::class,'show'])->name('products.show');
        Route::get('/products/{product}/edit',[AdminProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[AdminProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[AdminProductController::class,'destroy'])->name('products.destroy');
        Route::post('/products/{product}/duplicate',[AdminProductController::class,'duplicate'])->name('products.duplicate');
        Route::post('/products/{product}/archive',[AdminProductController::class,'archive'])->name('products.archive');
        Route::post('/products/{product}/restore',[AdminProductController::class,'restore'])->name('products.restore');
        Route::post('/products/{product}/inventory/adjust',[AdminProductController::class,'adjustInventory'])->name('products.inventory.adjust');
        Route::post('/products/{product}/images/reorder',[AdminProductController::class,'reorderImages'])->name('products.images.reorder');
        Route::post('/products/{product}/images/upload',[AdminProductController::class,'uploadImageAjax'])->name('products.images.upload');
        Route::post('/products/{product}/images/make-primary',[AdminProductController::class,'makePrimaryImage'])->name('products.images.make-primary');
        Route::delete('/products/{product}/images/{image}',[AdminProductController::class,'deleteImage'])->name('products.images.delete');

        Route::get('/orders',[AdminOrderController::class,'index'])->name('orders.index');
        Route::get('/orders/locations/cascading',[AdminOrderController::class,'locationsCascading'])->name('orders.locations.cascading');
        Route::get('/orders/export',[AdminOrderController::class,'export'])->name('orders.export');
        Route::post('/orders/bulk-action',[AdminOrderController::class,'bulkAction'])->name('orders.bulk-action');
        Route::get('/orders/{order}',[AdminOrderController::class,'show'])->name('orders.show');
        Route::put('/orders/{order}',[AdminOrderController::class,'update'])->name('orders.update');
        Route::patch('/orders/{order}/source',[AdminOrderController::class,'updateSource'])->name('orders.update-source');
        Route::patch('/orders/{order}/fulfilment',[AdminOrderController::class,'updateFulfilment'])->name('orders.update-fulfilment');
        Route::post('/orders/{order}/cancel',[AdminOrderController::class,'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/refund',[AdminOrderController::class,'refund'])->name('orders.refund');
        Route::post('/orders/{order}/mark-as-paid',[AdminOrderController::class,'markAsPaid'])->name('orders.mark-as-paid');
        Route::post('/orders/{order}/note',[AdminOrderController::class,'addNote'])->name('orders.note');
        Route::patch('/orders/{order}/address',[AdminOrderController::class,'updateAddress'])->name('orders.update-address');
        Route::get('/orders/{order}/invoice',[AdminOrderController::class,'invoice'])->name('orders.invoice');
        Route::post('/orders/{order}/returns',[AdminOrderController::class,'storeReturn'])->name('orders.returns.store');
        Route::patch('/orders/{order}/returns/{return}',[AdminOrderController::class,'updateReturn'])->name('orders.returns.update');

        Route::get('/customers',[AdminCustomerController::class,'index'])->name('customers.index');
        Route::get('/customers/export',[AdminCustomerController::class,'export'])->name('customers.export');
        Route::post('/customers/bulk-action',[AdminCustomerController::class,'bulkAction'])->name('customers.bulk-action');
        Route::get('/customers/{user}',[AdminCustomerController::class,'show'])->name('customers.show');
        Route::post('/customers/{user}/tags',[AdminCustomerController::class,'updateTags'])->name('customers.tags');
        Route::post('/customers/{user}/status',[AdminCustomerController::class,'updateStatus'])->name('customers.status');
        Route::post('/customers/{user}/notes',[AdminCustomerController::class,'storeNote'])->name('customers.notes.store');
        Route::delete('/customers/{user}/notes/{note}',[AdminCustomerController::class,'destroyNote'])->name('customers.notes.destroy');

        Route::get('/shipping',[AdminShippingController::class,'index'])->name('shipping.index');
        Route::post('/shipping',[AdminShippingController::class,'store'])->name('shipping.store');
        Route::post('/shipping/{shippingMethod}/rates',[AdminShippingController::class,'rate'])->name('shipping.rate');
        Route::get('/analytics/shipping-locations', [AddressAnalyticsController::class, 'index'])->name('analytics.shipping-locations');
        Route::get('/analytics/shipping-locations/heatmap', [AddressAnalyticsController::class, 'heatmap'])->name('analytics.shipping-locations.heatmap');
        Route::get('/analytics/shipping-locations/geojson', [AddressAnalyticsController::class, 'geojson'])->name('analytics.shipping-locations.geojson');

        // Coupons & Promotions Module
        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/export', [AdminCouponController::class, 'export'])->name('coupons.export');
        Route::post('/coupons/bulk-action', [AdminCouponController::class, 'bulkAction'])->name('coupons.bulk-action');
        Route::get('/coupons/{coupon}', [AdminCouponController::class, 'show'])->name('coupons.show');
        Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
        Route::post('/coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

        Route::get('/media',[AdminMediaController::class,'index'])->name('media.index');
        Route::post('/media',[AdminMediaController::class,'store'])->name('media.store');
        Route::delete('/media/{media}',[AdminMediaController::class,'destroy'])->name('media.destroy');

        Route::get('/cms',[AdminCmsController::class,'index'])->name('cms.index');
        Route::post('/cms',[AdminCmsController::class,'storePage'])->name('cms.store');
        Route::put('/cms/{page}',[AdminCmsController::class,'update'])->name('cms.update');
        Route::delete('/cms/{page}',[AdminCmsController::class,'destroyPage'])->name('cms.destroy');
        Route::get('/cms/{page}/sections',[AdminCmsController::class,'sections'])->name('cms.sections');
        Route::get('/cms/{page}/preview',[AdminCmsController::class,'preview'])->name('cms.preview');
        Route::post('/cms/{page}/sections',[AdminCmsController::class,'storeSection'])->name('cms.sections.store');
        Route::post('/cms/{page}/sections/reorder',[AdminCmsController::class,'reorderSections'])->name('cms.sections.reorder');
        Route::put('/cms/sections/{section}',[AdminCmsController::class,'updateSection'])->name('cms.sections.update');
        Route::post('/cms/sections/{section}/toggle-active',[AdminCmsController::class,'toggleSectionActive'])->name('cms.sections.toggle');
        Route::post('/cms/sections/{section}/duplicate',[AdminCmsController::class,'duplicateSection'])->name('cms.sections.duplicate');
        Route::delete('/cms/sections/{section}',[AdminCmsController::class,'destroySection'])->name('cms.sections.destroy');
        Route::get('/cms/media/picker',[AdminCmsController::class,'mediaPicker'])->name('cms.media.picker');
        Route::post('/cms/media/upload',[AdminCmsController::class,'uploadMedia'])->name('cms.media.upload');

        Route::get('/inventory',[AdminInventoryController::class,'index'])->name('inventory.index');
        Route::get('/inventory/export',[AdminInventoryController::class,'export'])->name('inventory.export');
        Route::get('/inventory/history',[AdminInventoryController::class,'history'])->name('inventory.history');
        Route::get('/inventory/{inventory}',[AdminInventoryController::class,'show'])->name('inventory.show');
        Route::post('/inventory/{inventory}/adjust',[AdminInventoryController::class,'adjust'])->name('inventory.adjust');
        Route::get('/whatsapp/templates',[AdminWhatsAppTemplateController::class,'index'])->name('whatsapp.templates');
        Route::post('/whatsapp/templates',[AdminWhatsAppTemplateController::class,'store'])->name('whatsapp.templates.store');
        Route::put('/whatsapp/templates/{template}',[AdminWhatsAppTemplateController::class,'update'])->name('whatsapp.templates.update');
        Route::delete('/whatsapp/templates/{template}',[AdminWhatsAppTemplateController::class,'destroy'])->name('whatsapp.templates.destroy');
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
        Route::get('/reset-operations/profiles/{resetProfile}',[ResetOperationsController::class,'show'])->name('reset.profiles.show');
        Route::get('/reset-operations/re-entry',[ResetOperationsController::class,'reentry'])->name('reset.reentry');
        Route::post('/reset-operations/re-entry/{reentryRequest}/approve',[ResetOperationsController::class,'approveReentry'])->name('reset.reentry.approve');
        Route::post('/reset-operations/re-entry/{reentryRequest}/reject',[ResetOperationsController::class,'rejectReentry'])->name('reset.reentry.reject');
        Route::post('/reset-operations/safety/{safetyFlag}/resolve',[ResetOperationsController::class,'resolveSafety'])->name('reset.safety.resolve');
        Route::get('/reset-operations/testimonials',[ResetOperationsController::class,'testimonials'])->name('reset.testimonials');
        Route::post('/reset-operations/testimonials/{testimonial}',[ResetOperationsController::class,'moderateTestimonial'])->name('reset.testimonials.moderate');

        // Analytics Routes
        Route::get('/analytics/overview', [AdminAnalyticsController::class, 'overview'])->name('analytics.overview');
        Route::get('/analytics/sales', [AdminAnalyticsController::class, 'sales'])->name('analytics.sales');
        Route::get('/analytics/sales/data', [AdminAnalyticsController::class, 'salesData'])->name('analytics.sales.data');
        Route::get('/analytics/sales/export', [AdminAnalyticsController::class, 'salesExport'])->name('analytics.sales.export');
        Route::get('/analytics/products', [AdminAnalyticsController::class, 'products'])->name('analytics.products');
        Route::get('/analytics/products/data', [AdminAnalyticsController::class, 'productsData'])->name('analytics.products.data');
        Route::get('/analytics/products/export', [AdminAnalyticsController::class, 'productsExport'])->name('analytics.products.export');
        Route::get('/analytics/customers', [AdminAnalyticsController::class, 'customers'])->name('analytics.customers');
        Route::get('/analytics/marketing', [AdminAnalyticsController::class, 'marketing'])->name('analytics.marketing');

        // Marketing Campaigns
        Route::get('/marketing/campaigns', [AdminCampaignController::class, 'index'])->name('marketing.campaigns');
        Route::post('/marketing/campaigns', [AdminCampaignController::class, 'store'])->name('marketing.campaigns.store');

        // Settings Routes
        Route::get('/settings/users', [AdminSettingsController::class, 'users'])->name('settings.users');
        Route::post('/settings/users', [AdminSettingsController::class, 'storeUser'])->name('settings.users.store');
        Route::put('/settings/users/{user}/role', [AdminSettingsController::class, 'updateUserRole'])->name('settings.users.update-role');
        Route::get('/settings/roles', [AdminSettingsController::class, 'roles'])->name('settings.roles');
        Route::post('/settings/roles/permissions', [AdminSettingsController::class, 'syncRolesPermissions'])->name('settings.roles.sync-permissions');
        Route::get('/settings/system', [AdminSettingsController::class, 'system'])->name('settings.system');
        Route::post('/settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('settings.clear-cache');
    });
});

// Dynamic CMS Public Pages Catch-All
Route::get('/{slug}', [HomeController::class, 'showPage'])
    ->where('slug', '[a-zA-Z0-9\-_]+')
    ->name('cms.page');

