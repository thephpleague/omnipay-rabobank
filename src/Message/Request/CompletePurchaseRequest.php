<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Message\Response\CompletePurchaseResponse;

/**
 * Create a payment with the Rabobank OmniKassa API.
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
