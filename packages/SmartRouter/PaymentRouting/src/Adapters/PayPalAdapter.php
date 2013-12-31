<?php

namespace SmartRouter\PaymentRouting\Adapters;

use GuzzleHttp\Client;
use SmartRouter\PaymentRouting\Exceptions\PaymentProcessingException;

class PayPalAdapter extends PaymentGatewayAdapter
{
    protected $httpClient;

    public function __construct($gateway)
    {
        parent::__construct($gateway);
        $this->httpClient = new Client([
            'base_uri' => 'https://api-m.sandbox.paypal.com', // Use live URL for production
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function getAccessToken()
    {
        $client = new Client(['base_uri' => 'https://api-m.sandbox.paypal.com']);
        $response = $client->post('/v1/oauth2/token', [
            'auth' => [$this->gateway['client_id'], $this->gateway['secret']],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
        $body = json_decode($response->getBody(), true);
        return $body['access_token'];
    }

    public function charge($amount, $currency, $source)
    {
        try {
            $response = $this->httpClient->post('/v2/checkout/orders', [
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => $amount,
                        ],
                    ]],
                    'application_context' => [
                        'return_url' => $source['callback_url'],
                        'cancel_url' => $source['cancel_url'],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            if ($body['status'] !== 'CREATED') {
                throw new PaymentProcessingException('PayPal Charge Error: ' . $body['message']);
            }

            return $body;
        } catch (\Exception $e) {
            throw new PaymentProcessingException('PayPal Charge Error: ' . $e->getMessage());
        }
    }
}
