<?php

namespace Omnipay\Rabobank;

use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Request\StatusRequest;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp(): void
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

    public function testRequestsFromOneGatewayShareASingleAccessToken()
    {
        $gateway = $this->createGatewayWithRefreshToken('refresh-a');
        $this->setMockHttpResponse('RefreshSuccess.txt');

        $this->assertSame('access-token', $gateway->purchase()->getAccessToken());
        $this->assertSame('access-token', $gateway->purchase()->getAccessToken());
        $this->assertSame(['Bearer refresh-a'], $this->authorizationHeadersSent());
    }

    public function testEachGatewayFetchesItsOwnAccessToken()
    {
        $first = $this->createGatewayWithRefreshToken('refresh-a');
        $second = $this->createGatewayWithRefreshToken('refresh-b');
        $this->setMockHttpResponse(['RefreshSuccess.txt', 'RefreshSuccess.txt']);

        $first->purchase()->getAccessToken();
        $second->purchase()->getAccessToken();

        $this->assertSame(['Bearer refresh-a', 'Bearer refresh-b'], $this->authorizationHeadersSent());
    }

    public function testSetRefreshTokenDiscardsCachedAccessToken()
    {
        $this->gateway->setAccessToken('access-token');

        $this->gateway->setRefreshToken('refresh-b');

        $this->assertNull($this->gateway->getAccessToken());
    }

    public function testSetTestModeDiscardsCachedAccessToken()
    {
        $this->gateway->setAccessToken('access-token');

        $this->gateway->setTestMode(true);

        $this->assertNull($this->gateway->getAccessToken());
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

    private function createGatewayWithRefreshToken($refreshToken)
    {
        $gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $gateway->setRefreshToken($refreshToken);

        return $gateway;
    }

    private function authorizationHeadersSent()
    {
        return array_map(function ($request) {
            return $request->getHeaderLine('Authorization');
        }, $this->getMockedRequests());
    }
}
