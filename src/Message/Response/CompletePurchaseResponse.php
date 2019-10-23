<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Rabobank\Message\Request\AbstractRabobankRequest;

class CompletePurchaseResponse extends PurchaseResponse
{
    public function test($method = 'test')
    {
        echo "<pre>";
        print_r($method . PHP_EOL);
        print_r($this->data);
        echo "</pre>";
        exit();
    }

    public function isSuccessful()
    {
        $this->test('isSuccessful');
    }

    public function getCode()
    {
        $this->test('getCode');
    }

    public function getMessage()
    {
        $this->test('getMessage');
    }

    public function getStatus()
    {
        $this->test('getStatus');
    }

    public function getTransactionId()
    {
        $this->test('getTransactionId');
    }

    public function getPaymentMethod()
    {
        $this->test('getPaymentMethod');
    }

    public function getAuthorisationId()
    {
        $this->test('getAuthorisationId');
    }

    public function getOrderId()
    {
        $this->test('getOrderId');
    }
}
