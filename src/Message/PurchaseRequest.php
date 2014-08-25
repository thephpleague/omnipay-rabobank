<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Rabobank Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public $testEndpoint = 'https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet';
    public $liveEndpoint = 'https://payment-webinit.omnikassa.rabobank.nl/paymentServlet';

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
    
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    public function getData()
    {
        $this->validate('merchantId', 'keyVersion', 'secretKey', 'amount', 'returnUrl', 'currency');
        
        $transRef = $this->getTransactionReference() ?: $this->getTransactionId();

        $data = array();
        $data['Data'] = implode(
            '|',
            array(
                'amount='.$this->getAmountInteger(),
                'currencyCode='.$this->getCurrencyNumeric(),
                'merchantId='.$this->getMerchantId(),
                'normalReturnUrl='.$this->getReturnUrl(),
                'automaticResponseUrl='.($this->getNotifyUrl() ?: $this->getReturnUrl()),
                'transactionReference='.$transRef,
                'keyVersion='.$this->getKeyVersion(),
                'paymentMeanBrandList='.$this->getPaymentMethod(),
                'orderId='.$this->getOrderId(),
            )
        );
        $data['InterfaceVersion'] = 'HP_1.0';

        return $data;
    }

    public function generateSignature($data)
    {
        if (empty($data['Data'])) {
            throw new InvalidRequestException('Missing Data parameter');
        }

        return hash('sha256', $data['Data'].$this->getSecretKey());
    }

    public function sendData($data)
    {
        $data['Seal'] = $this->generateSignature($data);

        return $this->response = new PurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
