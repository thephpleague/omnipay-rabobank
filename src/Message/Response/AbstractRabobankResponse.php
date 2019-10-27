<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Rabobank\Exception\InvalidSignatureException;
use Omnipay\Rabobank\Message\Request\AbstractRabobankRequest;

class AbstractRabobankResponse extends AbstractResponse
{
    /**
     * @var AbstractRabobankRequest
     */
    protected $request;

    /**
     * @param AbstractRabobankRequest $request
     * @param array $data
     *
     * @throws InvalidSignatureException
     * @throws InvalidResponseException
     */
    public function __construct(AbstractRabobankRequest $request, $data)
    {
        parent::__construct($request, $data);
        $this->validateResponse();
        $this->validateSignature();
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return isset($this->data['signature']);
    }

    /**
     * Check if we've received an error
     *
     * @throws InvalidResponseException
     */
    protected function validateResponse()
    {
        if (isset($this->data['errorCode']) && isset($this->data['consumerMessage'])) {
            throw new InvalidResponseException($this->data['consumerMessage'], $this->data['errorCode']);
        }
    }

    /**
     * @throws InvalidSignatureException
     */
    protected function validateSignature()
    {
        if (!isset($this->data['signature'])) {
            return;
        }
        
        $signatureData = $this->data;
        unset($signatureData['signature']);
        unset($signatureData['timestamp']);

        $signature = $this->request->gateway->generateSignature($this->flattenData($signatureData));

        if (!hash_equals($signature, $this->data['signature'])) {
            throw new InvalidSignatureException('Signature returned from server is invalid');
        }
    }

    protected function flattenData(array $data)
    {
        $flattened = [];
        foreach ($data as $value) {
            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->flattenData($value));
                continue;
            }
            $flattened[] = $value;
        }

        return $flattened;
    }
}
