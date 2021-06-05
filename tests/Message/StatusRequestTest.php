<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Rabobank\Gateway;
use Omnipay\Rabobank\Message\Request\StatusRequest;
use Omnipay\Rabobank\Message\Response\StatusResponse;
use Omnipay\Rabobank\Order;
use Omnipay\Tests\TestCase;

class StatusRequestTest extends TestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var StatusRequest
     */
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway();
        $this->gateway->setSigningKey(base64_encode('secret'));

        $this->request = new StatusRequest($this->getHttpClient(), $this->getHttpRequest(), $this->gateway);
        $this->request->initialize(array('notificationToken' => 'token'));
    }

    public function testGetData(): void
    {
        $data = $this->request->getData();
        $this->assertEquals([], $data);
    }

    public function testSendSuccess(): void
    {
        $this->setMockHttpResponse('StatusSuccess.txt');

        /** @var StatusResponse $response */
        $response = $this->request->send();

        $this->assertInstanceOf(StatusResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->getMoreStatusesAvailable());

        $orders = $response->getOrders();
        $this->assertCount(1, $response->getOrders());

        $order = reset($orders);
        $this->assertInstanceOf(Order::class, $order);

        $orderExpected = new Order();
        $orderExpected->merchantOrderId = 'order123';
        $orderExpected->omnikassaOrderId = '1d0a95f4-2589-439b-9562-c50aa19f9caf';
        $orderExpected->poiId = '2004';
        $orderExpected->orderStatus = 'IN_PROGRESS';
        $orderExpected->orderStatusDateTime = '2018-11-22T13:20:03.157+01:00';
        $orderExpected->errorCode = '';
        $orderExpected->paidAmount = [
            'currency' => 'USD',
            'amount' => '2000'
        ];
        $orderExpected->totalAmount = [
            'currency' => 'EUR',
            'amount' => '4999'
        ];

        $this->assertEquals($orderExpected, $order);
    }
}
