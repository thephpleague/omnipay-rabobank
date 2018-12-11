<?php

namespace Omnipay\Rabobank;

use Omnipay\Rabobank\Gateway;
use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Request\StatusRequest;
use Omnipay\Tests\GatewayTestCase;

/**
 * @method assertInstanceOf($string, $request)
 * @method assertSame($int, $getAmountInteger)
 */
class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway();
        $this->gateway->setSigningKey(base64_encode('secret'));
    }

    public function testPurchase()
    {
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchase(array('amount' => '10.00', 'currency' => 'EUR'));

        $this->assertInstanceOf(PurchaseRequest::class, $request);
        $this->assertSame(1000, $request->getAmountInteger());
        $this->assertSame('EUR', $request->getCurrency());
    }

    public function testStatus()
    {
        /** @var StatusRequest $request */
        $request = $this->gateway->status(array('notificationToken' => 'secret'));

        $this->assertInstanceOf(StatusRequest::class, $request);
        $this->assertSame('secret', $request->getNotificationToken());
    }

    public function testGenerateSignature()
    {
        $data = [
            date('c'),
            '6',
            'EUR',
            1000,
            'EN',
            '',
            'https://www.example.com/return',
            'IDEAL',
            'FORCE_ONCE'
        ];

        $expected = hash_hmac('sha512', implode(',', $data), 'secret');
        $this->assertSame($expected, $this->gateway->generateSignature($data));

        $data = [
            true,
            false,
            0,
            1,
            .1
        ];

        $signatureData = [
            'true',
            'false',
            '0',
            '1',
            '0.1'
        ];

        $expected = hash_hmac('sha512', implode(',', $signatureData), 'secret');
        $this->assertSame($expected, $this->gateway->generateSignature($data));
    }
}
