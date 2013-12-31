# Smart Payment Router for Laravel

## Smart routing system for payment gateways in Laravel

A composer package to streamline the payment processing workflow by implementing a smart routing system for payment gateways in Laravel.

## What is a Smart Router in Payment Gateways?

Smart routing allows merchants to dynamically route payments across different payment processors to maximize the chance of payment authorization.

### A Simpler Explanation:

A smart router in payment gateways is like someone or a solution that helps you choose the best road or bus to get to your destination.

Imagine you're at a busy junction, and you need to get to your destination (like a website or a bank). But, there are many buses and roads (payment methods) to choose from, and you don't know which one is the best.

That's where the smart router comes in! It looks at the traffic (payment requests) and chooses the fastest and safest road (payment method) to get you to your destination.

In payment gateways, a smart router helps figure out the best way to process payments, making sure they go through quickly and securely. It helps you to save time and money.

## Features

- Dynamic routing across multiple payment gateways
- Easy configuration and setup
- Seamless integration with Laravel applications
- Supports adding custom gateways

## Installation

You can install the package via composer:

**composer require smartrouter/payment-routing**

Next, publish the configuration file:

php artisan vendor:publish --provider="SmartRouter\PaymentRouting\Providers\PaymentRoutingServiceProvider" --tag="config"

## Configuration

After publishing the configuration file, you can find it at **config/payment.php**. You can define your payment gateways and their settings here.

Example configuration:

return [
    'gateways' => [
        'paystack' => [
            'active' => true,
            'countries' => ['NG'],
            'currencies' => ['NGN'],
            'cost' => 1,
            'reliability' => 1,
        ],
        // Add other gateways as needed
    ],
];


## Running Tests

To run the package's tests, use the following command:

vendor/bin/phpunit --testsuite=smart-router

## Contributing

Contributions are welcome! Please submit a pull request for any bug fixes or improvements.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Joy Fajendagba](https://github.com/Fajendagba)

## Support

If you encounter any issues or have any questions, feel free to open an issue on the [GitHub repository](https://github.com/Fajendagba/smart-payment-router).
