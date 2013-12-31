<?php

return [
    'routing' => [
        'priority' => ['cost', 'reliability'], // Define your priority criteria here
    ],
    'gateways' => [
        'paystack' => [
            'key' => 'paystack',
            'name' => 'Paystack',
            'class' => \SmartRouter\PaymentRouting\Adapters\PaystackAdapter::class,
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        ],
        'flutterwave' => [
            'key' => 'flutterwave',
            'name' => 'Flutterwave',
            'class' => \SmartRouter\PaymentRouting\Adapters\FlutterwaveAdapter::class,
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
        ],
        'stripe' => [
            'key' => 'stripe',
            'name' => 'Stripe',
            'class' => \SmartRouter\PaymentRouting\Adapters\StripeAdapter::class,
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'public_key' => env('STRIPE_PUBLIC_KEY'),
        ],
        'paypal' => [
            'key' => 'paypal',
            'name' => 'PayPal',
            'class' => \SmartRouter\PaymentRouting\Adapters\PayPalAdapter::class,
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
        ],
    ],
];
