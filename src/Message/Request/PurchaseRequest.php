<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Rabobank\Exception\DescriptionToLongException;
use Omnipay\Rabobank\Exception\InvalidLanguageCodeException;
use Omnipay\Rabobank\Message\Response\PurchaseResponse;

/**
 * Create a payment with the Rabobank OmniKassa API.
 */
class PurchaseRequest extends AbstractRabobankRequest
{

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getParameter('description');
    }

    /**
     * @param string $value
     * @return $this
     * @throws DescriptionToLongException
     */
    public function setDescription($value)
    {
        if(strlen($value) > 35) {
            throw new DescriptionToLongException('Description can only be 35 characters long');
        }

        return $this->setParameter('description', $value);
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->getParameter('languageCode');
    }

    /**
     * Must be a valid ISO 639-1 language code
     *
     * @param string $value
     * @return $this
     * @throws InvalidLanguageCodeException
     */
    public function setLanguageCode($value)
    {
        if(strlen($value) > 2) {
            throw new InvalidLanguageCodeException('Language code must be a valid ISO 639-1 language code');
        }

        return $this->setParameter('languageCode', $value);
    }


    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('refreshToken', 'orderId', 'amount', 'currency', 'returnUrl');

        $data = [];

        $data['timestamp'] = date('c');
        $data['merchantOrderId'] = $this->getOrderId();
        $data['description'] = $this->getDescription();
        $data['amount'] = $this->createAmountObject();
        $data['language'] = $this->getLanguageCode();
        $data['merchantReturnURL'] = $this->getReturnUrl();
        $data['paymentBrand'] = $this->getPaymentMethod();

        $data['signature'] = $this->generateSignature($data);

        return $data;
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     * @throws \Omnipay\Rabobank\Exception\InvalidSignatureException
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(self::POST, '/order/server/api/order', $data);

        return $this->response = new PurchaseResponse($this, $response);
    }

    protected function generateSignature(array $requestData)
    {
        $signatureData = [
            $requestData['timestamp'],
            $requestData['merchantOrderId'],
            $requestData['amount']['currency'],
            $requestData['amount']['amount'],
            isset($requestData['language']) ? $requestData['language'] : '',
            isset($requestData['description']) ? $requestData['description'] : '',
            $requestData['merchantReturnURL']
        ];

        if(isset($requestData['paymentBrand'])) {
            $signatureData[] = $requestData['paymentBrand'];
        }

        return $this->gateway->generateSignature($signatureData);
    }
}
