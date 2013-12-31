<?php

namespace SmartRouter\PaymentRouting\Services;

use SmartRouter\PaymentRouting\Exceptions\NoAvailableGatewayException;
use SmartRouter\PaymentRouting\Adapters\PaymentGatewayAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PaymentGateway
{
    protected $gateways;

    public function __construct()
    {
        $this->loadGateways();
    }

    protected function loadGateways()
    {
        $this->gateways = DB::table('gateways')->pluck('details', 'key')->map(function ($item) {
            return json_decode($item, true);
        })->toArray();
    }

    public function route($country, $currency)
    {
        $availableGateways = array_filter($this->gateways, function ($gateway) use ($country, $currency) {
            return $gateway['active'] &&
                in_array($country, $gateway['countries']) &&
                in_array($currency, $gateway['currencies']);
        });

        if (empty($availableGateways)) {
            throw new NoAvailableGatewayException('No available payment gateway for this transaction.');
        }

        usort($availableGateways, function ($a, $b) {
            $priority = config('payment.routing.priority');
            foreach ($priority as $criteria) {
                if ($a[$criteria] < $b[$criteria]) {
                    return -1;
                } elseif ($a[$criteria] > $b[$criteria]) {
                    return 1;
                }
            }
            return 0;
        });

        return array_shift($availableGateways);
    }

    public function charge($amount, $currency, $source, $country)
    {
        try {
            $gateway = $this->route($country, $currency);
            $adapter = $this->getAdapter($gateway);
            return $adapter->charge($amount, $currency, $source);
        } catch (NoAvailableGatewayException $e) {
            Log::error($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getAdapter($gateway)
    {
        $class = $gateway['class'];
        return new $class($gateway);
    }

    public function addGateway($key, $details)
    {
        DB::table('gateways')->updateOrInsert(['key' => $key], ['details' => json_encode($details)]);
        $this->loadGateways();
    }

    public function updateGateway($key, $details)
    {
        if (isset($this->gateways[$key])) {
            DB::table('gateways')->where('key', $key)->update(['details' => json_encode($details)]);
            $this->loadGateways();
        }
    }

    public function removeGateway($key)
    {
        if (isset($this->gateways[$key])) {
            DB::table('gateways')->where('key', $key)->delete();
            $this->loadGateways();
        }
    }

    public function getGateways()
    {
        return $this->gateways;
    }
}
