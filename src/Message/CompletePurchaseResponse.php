<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Rabobank Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /** @var array The internal (parsed) data */
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

    public function getMessage()
    {
        $code = $this->getCode();
        
        $messages = array(
            '00' => 'Transaction successful. Authorisation accepted (credit card).',
            '02' => 'Credit card authorisation limit exceeded. Contact the Support Team Rabo OmniKassa.',
            '03' => 'Invalid merchant contract.',
            '05' => 'Refused.',
            '12' => 'Invalid transaction. Check the fields in the payment request.',
            '14' => 'Invalid credit card number, invalid card security code, '.
                    'invalid card (MasterCard) or invalid Card Verification Value (MasterCard or VISA).',
            '17' => 'Cancellation of payment by user.',
            '24' => 'Invalid status.',
            '25' => 'Transaction not found in database.',
            '30' => 'Invalid format.',
            '34' => 'Fraud suspicion.',
            '40' => 'Operation not allowed for this merchant/webshop.',
            '60' => 'Awaiting status report.',
            '63' => 'Security problem detected. Transaction terminated.',
            '75' => 'Maximum number of attempts to enter credit card number (3) exceeded.',
            '90' => 'Rabo OmniKassa server temporarily unavailable.',
            '94' => 'Duplicate transaction.',
            '97' => 'Time period expired. Transaction refused.',
            '99' => 'Payment page temporarily unavailable.',
        );

        return isset($messages[$code]) ? $messages[$code] : null;
    }

    public function getStatus()
    {
        $code = $this->getCode();
        $status = array(
            '00' => 'SUCCESS',
            '17' => 'CANCELLED',
            '60' => 'PENDING',
            '97' => 'EXPIRED',
        );

        return isset($status[$code]) ? $status[$code] : 'FAILED';
    }

    public function getTransactionReference()
    {
        return isset($this->i_data['transactionReference']) ? $this->i_data['transactionReference'] : null;
    }
    
    public function getTransactionId()
    {
        return isset($this->i_data['orderId']) ? $this->i_data['orderId'] : null;
    }

    public function getPaymentMethod()
    {
        return isset($this->i_data['paymentMeanBrand']) ? $this->i_data['paymentMeanBrand'] : null;
    }

    public function getAuthorisationId()
    {
        return isset($this->i_data['authorisationId']) ? $this->i_data['authorisationId'] : null;
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
