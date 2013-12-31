<?php

namespace SmartRouter\PaymentRouting\Tests;

use Illuminate\Support\Facades\DB;
use SmartRouter\PaymentRouting\Services\PaymentGateway;
use SmartRouter\PaymentRouting\Exceptions\NoAvailableGatewayException;

class PaymentGatewayTest extends TestCase
{
    /** @test */
    public function it_can_route_to_an_available_gateway()
    {
        $this->setUpGateways();

        $paymentGateway = $this->app->make(PaymentGateway::class);

        $gateway = $paymentGateway->route('NG', 'NGN');

        $this->assertNotEmpty($gateway);
        $this->assertEquals('paystack', $gateway['key']);
    }

    /** @test */
    public function it_throws_exception_if_no_gateway_is_available()
    {
        $this->setUpNoGateways();

        $paymentGateway = $this->app->make(PaymentGateway::class);

        $this->expectException(NoAvailableGatewayException::class);

        $paymentGateway->route('NG', 'NGN');
    }

    protected function setUpGateways()
    {
        DB::table('gateways')->insert([
            'key' => 'paystack',
            'details' => json_encode([
                'active' => true,
                'countries' => ['NG'],
                'currencies' => ['NGN'],
                'cost' => 1,
                'reliability' => 1,
            ]),
        ]);
    }

    protected function setUpNoGateways()
    {
        DB::table('gateways')->truncate();
    }
}
