<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Rabobank Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{

    protected $i_data;
    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->i_data = $this->getInternalData();
    }

    public function isSuccessful()
    {
        return '00' === $this->getCode();
    }

    public function getCode()
    {
        return isset($this->i_data['responseCode']) ? $this->i_data['responseCode'] : null;
    }

    public function getTransactionId()
    {
        return isset($this->i_data['transactionReference']) ? $this->i_data['transactionReference'] : null;
    }

    public function getPaymentMethod()
    {
        return isset($this->i_data['paymentMeanBrand']) ? $this->i_data['paymentMeanBrand'] : null;
    }

    public function getAuthorisationId()
    {
        return isset($this->i_data['authorisationId']) ? $this->i_data['authorisationId'] : null;
    }

    public function getOrderId()
    {
        return isset($this->i_data['orderId']) ? $this->i_data['orderId'] : null;
    }

    /**
     * Decode ridiculous 'Data' response parameter
     */
    public function getInternalData()
    {
        if (!is_null($this->i_data)) {
            return $this->i_data;
        }
        
        if (empty($this->data['Data'])) {
            return;
        }

        $data = array();
        foreach (explode('|', $this->data['Data']) as $line) {
            $line = explode('=', $line, 2);
            if (!empty($line[0])) {
                $data[trim($line[0])] = isset($line[1]) ? trim($line[1]) : null;
            }
        }

        return $this->i_data = $data;
    }
}
