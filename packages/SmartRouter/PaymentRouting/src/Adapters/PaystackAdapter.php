<?php

namespace SmartRouter\PaymentRouting\Adapters;

use GuzzleHttp\Client;
use SmartRouter\PaymentRouting\Exceptions\PaymentProcessingException;

class PaystackAdapter extends PaymentGatewayAdapter
{
    protected $httpClient;

    public function __construct($gateway)
    {
        parent::__construct($gateway);
        $this->httpClient = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->gateway['secret_key'],
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function charge($amount, $currency, $source)
    {
        try {
            $response = $this->httpClient->post('/transaction/initialize', [
                'json' => [
                    'amount' => $amount * 100, // Paystack expects the amount in kobo
                    'email' => $source['email'],
                    'currency' => $currency,
                    'reference' => $source['reference'],
                    'callback_url' => $source['callback_url'],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            if ($body['status'] !== true) {
                throw new PaymentProcessingException('Paystack Charge Error: ' . $body['message']);
            }

            return $body;
        } catch (\Exception $e) {
            throw new PaymentProcessingException('Paystack Charge Error: ' . $e->getMessage());
        }
    }
}
