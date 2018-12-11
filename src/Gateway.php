<?php

namespace Omnipay\Rabobank;

use Omnipay\Common\AbstractGateway;
use Omnipay\Rabobank\Message\Request\PurchaseRequest;
use Omnipay\Rabobank\Message\Request\StatusRequest;

/**
 * Rabobank Gateway
 *
 * @link https://www.rabobank.nl/images/handleiding-api-koppeling-rabo-smartpin-en_29970886.pdf
 */
class Gateway extends AbstractGateway
{
    const SIGNING_HASH_ALGORITHM = 'sha512';

    public function getName()
    {
        return 'Rabobank OmniKassa';
    }

    /**
     * @return string
     */
    public function getSigningKey()
    {
        return $this->getParameter('signingKey');
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setSigningKey($value)
    {
        return $this->setParameter('signingKey', $value);
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->getParameter('refreshToken');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setRefreshToken($value)
    {
        return $this->setParameter('refreshToken', $value);
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateSignature(array $data)
    {
        $data = $this->massageSignatureData($data);

        return hash_hmac(static::SIGNING_HASH_ALGORITHM, implode(',', $data), base64_decode($this->getParameter('signingKey')));
    }

    protected function massageSignatureData(array $data)
    {
        return array_map(function($value) {
            if(is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            return (string) $value;
        }, $data);
    }

    /**
     * @param  array $parameters
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        /** @var PurchaseRequest $request */
        $request = $this->createRequest(PurchaseRequest::class, $parameters);

        return $request;
    }

    /**
     * @param  array $parameters
     * @return StatusRequest
     */
    public function status(array $parameters = [])
    {
        /** @var StatusRequest $request */
        $request = $this->createRequest(StatusRequest::class, $parameters);

        return $request;
    }

    protected function createRequest($class, array $parameters)
    {
        $obj = new $class($this->httpClient, $this->httpRequest, $this);

        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }


}
