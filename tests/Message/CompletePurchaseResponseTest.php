<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function testSuccess()
    {
        $data = array('Data' => 'responseCode=00');

        $response = new CompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('00', $response->getCode());
    }

    public function testEmpty()
    {
        $response = new CompletePurchaseResponse($this->getMockRequest(), array());

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getCode());
    }

    public function testGetInternalData()
    {
        $data = array('Data' => 'a=foo|b=bar|c|');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $data);

        $expected = array('a' => 'foo', 'b' => 'bar', 'c' => null);
        $this->assertSame($expected, $response->getInternalData());
    }

    public function testGetInternalDataEmpty()
    {
        $response = new CompletePurchaseResponse($this->getMockRequest(), array());
        $this->assertNull($response->getInternalData());
    }
    
    public function testGetData()
    {
        $data = array('Data' => 'orderId=6|transactionReference=5|authorisationId=123|paymentMeanBrand=IDEAL');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $data);
        
        $this->assertSame("6", $response->getOrderId());
        $this->assertSame("5", $response->getTransactionId());
        $this->assertSame("123", $response->getAuthorisationId());
        $this->assertSame("IDEAL", $response->getPaymentMethod());
    }
}
