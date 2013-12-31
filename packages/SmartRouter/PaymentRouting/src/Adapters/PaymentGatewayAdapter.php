<?php

namespace SmartRouter\PaymentRouting\Adapters;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use SmartRouter\PaymentRouting\Exceptions\PaymentProcessingException;

abstract class PaymentGatewayAdapter
{
    protected $gateway;

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
    }

    abstract public function charge($amount, $currency, $source);

    protected function encryptSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, config('smart-payment-router.sensitive_fields'))) {
                $data[$key] = Crypt::encryptString($value);
            }
        }
        return $data;
    }

    protected function decryptSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, config('smart-payment-router.sensitive_fields'))) {
                try {
                    $data[$key] = Crypt::decryptString($value);
                } catch (DecryptException $e) {
                    Log::error("Failed to decrypt sensitive data: {$e->getMessage()}");
                    throw new PaymentProcessingException("Error processing payment due to data corruption");
                }
            }
        }
        return $data;
    }
}
