<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Rabobank\Message\Response\CompletePurchaseResponse;

/**
 * Create a payment with the Rabobank OmniKassa API.
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        $data = $this->httpRequest->query->all();
        $data['order_id'] = $this->httpRequest->query->get('order_id');
        $data['status'] = $this->httpRequest->query->get('status');
        $data['signature'] = $this->httpRequest->query->get('order_id');
        return $data;
    }
    
    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
