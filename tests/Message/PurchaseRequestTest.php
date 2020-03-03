<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Rabobank\Gateway;
use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Response\PurchaseResponse;
use Omnipay\Tests\TestCase;

/**
 * @method assertRegExp($string, $timestamp)
 * @method assertEquals($string, $merchantOrderId)
 * @method assertInstanceOf($string, $response)
 * @method assertFalse($isSuccessful)
 * @method assertTrue($isRedirect)
 */
class PurchaseRequestTest extends TestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var PurchaseRequest
     */
    protected $request;

    public function setUp()
    {
        $this->gateway = new Gateway();
        $this->gateway->setSigningKey(base64_encode('secret'));

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest(), $this->gateway);
        $this->request->initialize(
            array(
                'refreshToken' => 'secret',
                'amount' => '10.00',
                'currency' => 'EUR',
                'returnUrl' => 'https://www.example.com/return',
                'orderId' => '1'
            )
        );
        $this->request->setAccessToken('secret');
    }

    public function testGetData()
    {
        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');
        $this->request->setLanguageCode('EN');

        $card = new CreditCard([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'address1' => 'Main street 123',
            'postcode' => '1234AA',
            'city' => 'Anytown',
            'country' => 'NL'
        ]);
        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertRegExp('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|(\+|-)\d{2}(:?\d{2})?)$/', $data['timestamp']);
        $this->assertEquals('6', $data['merchantOrderId']);
        $this->assertEquals(array(
            'amount' => 1000,
            'currency' => 'EUR'
        ), $data['amount']);
        $this->assertEquals('EN', $data['language']);
        $this->assertEquals('', $data['description']);
        $this->assertEquals('https://www.example.com/return', $data['merchantReturnURL']);
        $this->assertEquals(array(
            'firstName' => 'John',
            'middleName' => '',
            'lastName' => 'Doe',
            'street' => 'Main street 123',
            'postalCode' => '1234AA',
            'city' => 'Anytown',
            'countryCode' => 'NL',
        ), $data['shippingDetail']);
        $this->assertEquals('IDEAL', $data['paymentBrand']);
        $this->assertEquals('FORCE_ONCE', $data['paymentBrandForce']);

        $signatureData = array(
            $data['timestamp'],
            '6',
            'EUR',
            1000,
            'EN',
            '',
            'https://www.example.com/return',
            'John',
            '',
            'Doe',
            'Main street 123',
            '1234AA',
            'Anytown',
            'NL',
            'IDEAL',
            'FORCE_ONCE'
        );

        $signature = hash_hmac('sha512', implode(',', $signatureData), 'secret');
        $this->assertEquals($signature, $data['signature']);
    }

    public function testBaseUrl()
    {
        $this->gateway->setTestMode(false);
        $this->assertEquals('https://betalen.rabobank.nl/omnikassa-api/', $this->request->getBaseUrl());

        $this->gateway->setTestMode(true);
        $this->assertEquals('https://betalen.rabobank.nl/omnikassa-api-sandbox/', $this->request->getBaseUrl());

    }

    public function testDescription()
    {
        $this->request->setDescription('a description');
        $data = $this->request->getData();

        $this->assertEquals('a description', $data['description']);
    }

    /**
     * @expectedException \Omnipay\Rabobank\Exception\DescriptionToLongException
     * @expectedExceptionMessage Description can only be 35 characters long
     */
    public function testDescriptionToLong()
    {
        $this->request->setDescription('a very long description that is longer then 35 characters that is not allowed');
    }

    public function testLanguageCode()
    {
        $this->request->setLanguageCode('EN');
        $data = $this->request->getData();

        $this->assertEquals('EN', $data['language']);
    }

    /**
     * @expectedException \Omnipay\Rabobank\Exception\InvalidLanguageCodeException
     * @expectedExceptionMessage Language code must be a valid ISO 639-1 language code
     */
    public function testLanguageCodeInvalid()
    {
        $this->request->setLanguageCode('ENG');
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');

        /** @var PurchaseResponse $response */
        $response = $this->request->send();

        $this->assertInstanceOf(PurchaseResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('https://www.example.com/redirect', $response->getRedirectUrl());
    }

    /**
     * @expectedException \Omnipay\Rabobank\Exception\InvalidSignatureException
     * @expectedExceptionMessage Signature returned from server is invalid
     */
    public function testSendInvalidSignature()
    {
        $this->setMockHttpResponse('PurchaseInvalidSignature.txt');

        $this->request->setPaymentMethod('IDEAL');
        $this->request->setOrderId('6');

        $this->request->send();
    }
}
