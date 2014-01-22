<?php

namespace Omnipay\Rabobank;

use Omnipay\Common\AbstractGateway;

/**
 * Rabobank Gateway
 *
 * @link http://www.rabobank.nl/images/integratiehandleiding_en_12_2013_29451215.pdf
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Rabobank OmniKassa';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'keyVersion' => '',
            'secretKey' => '',
            'testMode' => '',
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getKeyVersion()
    {
        return $this->getParameter('keyVersion');
    }

    public function setKeyVersion($value)
    {
        return $this->setParameter('keyVersion', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Rabobank\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Rabobank\Message\CompletePurchaseRequest', $parameters);
    }
}
