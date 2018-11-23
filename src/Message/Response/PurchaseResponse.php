<?php

namespace Omnipay\Rabobank\Message\Response;

class PurchaseResponse extends AbstractRabobankResponse
{
    /**
     * When you do a `purchase` the request is never successful because
     * you need to redirect off-site to complete the purchase.
     *
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->data['redirectUrl'];
    }

}
