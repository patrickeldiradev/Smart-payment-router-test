<?php

namespace SmartRouter\PaymentRouting\Providers;

use Illuminate\Support\ServiceProvider;
use SmartRouter\PaymentRouting\Services\PaymentGateway;

class PaymentRoutingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaymentGateway::class, function ($app) {
            return new PaymentGateway();
        });

        // Merging package config with the application's config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/payment.php', 'payment'
        );
    }

    public function boot()
    {
        // Publishing the config file
        $this->publishes([
            __DIR__.'/../../config/payment.php' => config_path('payment.php'),
        ], 'config');

        // Loading migrations
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }
}