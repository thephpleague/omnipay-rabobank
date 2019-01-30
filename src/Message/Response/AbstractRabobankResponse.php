<?php

namespace Omnipay\Rabobank\Message\Response;

use Omnipay\Common\Message\AbstractResponse;
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
     */
    public function __construct(AbstractRabobankRequest $request, $data)
    {
        parent::__construct($request, $data);
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
     * @throws InvalidSignatureException
     */
    protected function validateSignature()
    {
        if (!isset($this->data['signature'])) {
            return;
        }
        
        $signatureData = [
            'redirectUrl' => $this->data['redirectUrl'] ?? '',
        ];

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
