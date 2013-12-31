<?php

namespace SmartRouter\PaymentRouting\Adapters;

use GuzzleHttp\Client;
use SmartRouter\PaymentRouting\Exceptions\PaymentProcessingException;

class StripeAdapter extends PaymentGatewayAdapter
{
    protected $httpClient;

    public function __construct($gateway)
    {
        parent::__construct($gateway);
        $this->httpClient = new Client([
            'base_uri' => 'https://api.stripe.com/v1',
            'auth' => [$this->gateway['secret_key'], ''],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
    }

    public function charge($amount, $currency, $source)
    {
        try {
            $response = $this->httpClient->post('/charges', [
                'json' => [
                    'amount' => $amount * 100, // Stripe expects the amount in cents
                    'currency' => $currency,
                    'source' => $source['token'],
                    'description' => 'Charge for ' . $source['email'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            if (isset($body['error'])) {
                throw new PaymentProcessingException('Stripe Charge Error: ' . $body['error']['message']);
            }

            return $body;
        } catch (\Exception $e) {
            throw new PaymentProcessingException('Stripe Charge Error: ' . $e->getMessage());
        }
    }
}
