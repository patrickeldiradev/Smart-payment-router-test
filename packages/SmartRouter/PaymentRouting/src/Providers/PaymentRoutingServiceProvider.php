<?php

namespace SmartRouter\PaymentRouting\Providers;

use Illuminate\Support\Facades\Log;
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
        Log::info('PaymentRoutingServiceProvider boot method executed');

        // Publishing the config file
        $this->publishes([
            __DIR__.'/../../config/payment.php' => config_path('payment.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../migrations' => database_path('migrations'),
        ], 'migrations');

        // Loading migrations
        $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }
}
