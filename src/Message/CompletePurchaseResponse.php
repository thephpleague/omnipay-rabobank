<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Rabobank Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return '00' === $this->getCode();
    }

    public function getCode()
    {
        $data = $this->getInternalData();

        return isset($data['responseCode']) ? $data['responseCode'] : null;
    }

    /**
     * Decode ridiculous 'Data' response parameter
     */
    public function getInternalData()
    {
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

        return $data;
    }
}
