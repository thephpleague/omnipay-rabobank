<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'merchantId' => 'user',
                'keyVersion' => 'ver',
                'secretKey' => 'shhhh',
                'amount' => '10.00',
                'currency' => 'EUR',
                'returnUrl' => 'https://www.example.com/return',
                'transactionId' => '5',
            )
        );
    }

    public function testGetData()
    {
        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');

        $data = $this->request->getData();

        $this->assertContains('amount=1000', $data['Data']);
        $this->assertContains('currencyCode=978', $data['Data']);
        $this->assertContains('merchantId=user', $data['Data']);
        $this->assertContains('keyVersion=ver', $data['Data']);
        $this->assertContains('normalReturnUrl=https://www.example.com/return', $data['Data']);
        $this->assertContains('automaticResponseUrl=https://www.example.com/return', $data['Data']);
        $this->assertContains('transactionReference=5', $data['Data']);
        $this->assertContains('paymentMeanBrandList=IDEAL', $data['Data']);
        $this->assertContains('orderId=6', $data['Data']);

        $this->assertSame('HP_1.0', $data['InterfaceVersion']);
    }

    public function testGenerateSignature()
    {
        $this->request->setSecretKey('secret');
        $data = array('Data' => 'foo');

        $expected = hash('sha256', 'foosecret');

        $this->assertSame($expected, $this->request->generateSignature($data));
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Missing Data parameter
     */
    public function testGenerateSignatureEmpty()
    {
        $this->request->setSecretKey('secret');

        $this->request->generateSignature(array());
    }

    public function testSend()
    {
        $response = $this->request->send();

        $this->assertInstanceOf('\Omnipay\Rabobank\Message\PurchaseResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());

        $data = $response->getRedirectData();
        $this->assertArrayHasKey('Data', $data);
        $this->assertArrayHasKey('Seal', $data);
    }

    public function testGetEndpoint()
    {
        $this->request->setTestMode(false);
        $this->assertContains('webinit.omnikassa', $this->request->getEndpoint());

        $this->request->setTestMode(true);
        $this->assertContains('webinit.simu.omnikassa', $this->request->getEndpoint());
    }
}
