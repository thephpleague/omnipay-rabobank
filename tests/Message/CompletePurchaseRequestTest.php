<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'merchantId' => 'user',
                'keyVersion' => 'ver',
                'secretKey' => 'shhhh',
                'amount' => '10.00',
                'currency' => 'EUR',
            )
        );
    }

    public function testGetData()
    {
        $httpData = array();
        $httpData['Data'] = 'foo';
        $httpData['Seal'] = hash('sha256', $httpData['Data'].'shhhh');
        $this->getHttpRequest()->request->replace($httpData);

        $this->assertSame($httpData, $this->request->getData());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Incorrect signature
     */
    public function testGetDataInvalid()
    {
        $httpData = array();
        $httpData['Data'] = 'foo';
        $httpData['Seal'] = 'invalid';
        $this->getHttpRequest()->request->replace($httpData);

        $this->assertSame($httpData, $this->request->getData());
    }

    public function testSend()
    {
        $httpData = array();
        $httpData['Data'] = 'foo=bar|responseCode=00';
        $httpData['Seal'] = hash('sha256', $httpData['Data'].'shhhh');
        $this->getHttpRequest()->request->replace($httpData);

        $response = $this->request->send();

        $this->assertInstanceOf('\Omnipay\Rabobank\Message\CompletePurchaseResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
        $this->assertSame($httpData, $response->getData());
    }
}
