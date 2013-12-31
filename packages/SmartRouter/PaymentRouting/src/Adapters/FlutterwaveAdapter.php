<?php

namespace SmartRouter\PaymentRouting\Adapters;

use GuzzleHttp\Client;
use SmartRouter\PaymentRouting\Adapters\PaymentGatewayAdapter;
use SmartRouter\PaymentRouting\Exceptions\PaymentProcessingException;

class FlutterwaveAdapter extends PaymentGatewayAdapter
{
    protected $httpClient;

    public function __construct($gateway)
    {
        parent::__construct($gateway);
        $this->httpClient = new Client([
            'base_uri' => 'https://api.flutterwave.com/v3',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->gateway['secret_key'],
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function charge($amount, $currency, $source)
    {
        try {
            $response = $this->httpClient->post('/payments', [
                'json' => [
                    'tx_ref' => $source['reference'],
                    'amount' => $amount,
                    'currency' => $currency,
                    'redirect_url' => $source['callback_url'],
                    'payment_type' => 'card',
                    'customer' => [
                        'email' => $source['email'],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            if ($body['status'] !== 'success') {
                throw new PaymentProcessingException('Flutterwave Charge Error: ' . $body['message']);
            }

            return $body;
        } catch (\Exception $e) {
            throw new PaymentProcessingException('Flutterwave Charge Error: ' . $e->getMessage());
        }
    }
}
