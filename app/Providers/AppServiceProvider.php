<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\RazorpayPaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, RazorpayPaymentGateway::class);
    }

    public function boot(): void {}
}
