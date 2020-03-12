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

    const PAYMENT_METHOD_ENFORCE_ONCE = 'FORCE_ONCE';
    const PAYMENT_METHOD_ENFORCE_ALWAYS = 'FORCE_ALWAYS';

    public function initialize(array $parameters = [])
    {

        if (!isset($parameters['enforcePaymentMethod'])) {
            $parameters['enforcePaymentMethod'] = static::PAYMENT_METHOD_ENFORCE_ONCE;
        }

        return parent::initialize($parameters);
    }


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
        if (strlen($value) > 35) {
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
        if (strlen($value) > 2) {
            throw new InvalidLanguageCodeException('Language code must be a valid ISO 639-1 language code');
        }

        return $this->setParameter('languageCode', $value);
    }

    /**
     * @return string
     */
    public function getEnforcePaymentMethod()
    {
        return $this->getParameter('enforcePaymentMethod');
    }

    /**
     * Set if the payment method should be enforced.
     *
     * This field can be used to send or,
     * after a failed payment, the consumer can or can not
     * select another payment method to still pay the payment.
     * Valid values are:
     *  static::PAYMENT_METHOD_ENFORCE_ONCE
     *  static::PAYMENT_METHOD_ENFORCE_ALWAYS
     * In the case of FORCE_ONCE, the indicated paymentMethod is
     * only enforced on the first transaction. If this fails,
     * the consumer can still choose another payment method.
     * When FORCE_ALWAYS is chosen, the consumer can
     * not choose another payment method
     *
     * @param string $value
     * @return $this
     */
    public function setEnforcePaymentMethod($value)
    {
        return $this->setParameter('enforcePaymentMethod', $value);
    }


    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('orderId', 'amount', 'currency', 'returnUrl');

        $data = [];

        $data['timestamp'] = date('c');
        $data['merchantOrderId'] = $this->getOrderId();
        $data['description'] = $this->getDescription();
        $data['amount'] = $this->createAmountObject();
        $data['language'] = $this->getLanguageCode();
        $data['merchantReturnURL'] = $this->getReturnUrl();
        $data['paymentBrand'] = $this->getPaymentMethod();

        // PaymentBrandForce should only be set when there is a PaymentBrand
        if ($data['paymentBrand']) {
            $data['paymentBrandForce'] = $this->getEnforcePaymentMethod();
        }

        $card = $this->getCard();
        if (isset($card)) {
            $data['shippingDetail'] = [
                'firstName' => (string)$card->getFirstName(),
                'middleName' => '',
                'lastName' => (string)$card->getLastName(),
                'street' => (string)$card->getAddress1(),
                'postalCode' => (string)$card->getPostcode(),
                'city' => (string)$card->getCity(),
                'countryCode' => (string)$card->getCountry(),
            ];
        }

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
            $requestData['merchantReturnURL'],
        ];

        if (isset($requestData['shippingDetail'])) {
            $signatureData[] = $requestData['shippingDetail'];
        }

        if (isset($requestData['paymentBrand'])) {
            $signatureData[] = $requestData['paymentBrand'];
        }

        if (isset($requestData['paymentBrandForce'])) {
            $signatureData[] = $requestData['paymentBrandForce'];
        }

        return $this->gateway->generateSignature($signatureData);
    }
}
