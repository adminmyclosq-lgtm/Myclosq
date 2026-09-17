<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\RazorpayPaymentGateway;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, RazorpayPaymentGateway::class);
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $isMyClosq = in_array(request()->getHost(), ['myclosq.com', 'www.myclosq.com']);
            $view->with('isMyClosq', $isMyClosq);
            $view->with('brandName', $isMyClosq ? 'My CLOSQ' : 'Gut Reset');
        });
    }
}
