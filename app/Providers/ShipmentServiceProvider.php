<?php

namespace App\Providers;

use App\Contracts\ShipmentProviderInterface;
use App\Services\ManualShipmentProvider;
use Illuminate\Support\ServiceProvider;

class ShipmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ShipmentProviderInterface::class, ManualShipmentProvider::class);
    }

    public function boot(): void {}
}
