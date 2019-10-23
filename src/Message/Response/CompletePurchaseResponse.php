<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Rabobank\Message\Request\AbstractRabobankRequest;

class CompletePurchaseResponse extends PurchaseResponse
{
    public function __construct(AbstractRabobankRequest $request, $data)
    {
        parent::__construct($request, $data);

        echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit();
    }
}
