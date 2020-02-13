<?php

namespace Omnipay\Rabobank\Message\Response;

class CompletePurchaseResponse extends AbstractRabobankResponse
{
    public function isSuccessful()
    {
        return isset($this->data['status']) && $this->data['status'] === 'COMPLETED';
    }

    public function isCancelled()
    {
        return isset($this->data['status']) && $this->data['status'] === 'CANCELLED';
    }

    public function isExpired()
    {
        return isset($this->data['status']) && $this->data['status'] === 'EXPIRED';
    }

    public function getOrderId()
    {
        return isset($this->data['order_id']) ? $this->data['order_id'] : null;
    }
}
