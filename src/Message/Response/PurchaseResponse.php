<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractRabobankResponse implements RedirectResponseInterface
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
