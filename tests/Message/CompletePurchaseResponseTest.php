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
        $this->assertSame('SUCCESS', $response->getStatus());
        $this->assertSame('Transaction successful. Authorisation accepted (credit card).', $response->getMessage());
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
        $data = array('Data' => 'orderId=6|transactionReference=5|authorisationId=123|paymentMeanBrand=IDEAL|responseCode=17');
        $response = new CompletePurchaseResponse($this->getMockRequest(), $data);
        
        $this->assertSame("6", $response->getOrderId());
        $this->assertSame("5", $response->getTransactionId());
        $this->assertSame("123", $response->getAuthorisationId());
        $this->assertSame("IDEAL", $response->getPaymentMethod());
        $this->assertSame("CANCELLED", $response->getStatus());
        $this->assertSame("Cancellation of payment by user.", $response->getMessage());
    }
    
    public function testUnknownResponseCode()
    {
        $data = array('Data' => 'responseCode=AA');

        $response = new CompletePurchaseResponse($this->getMockRequest(), $data);

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('AA', $response->getCode());
        $this->assertSame('FAILED', $response->getStatus());
        $this->assertNull($response->getMessage());
    }
}
