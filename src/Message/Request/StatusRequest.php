<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Rabobank\Message\Response\StatusResponse;

/**
 * Fetch statuses of orders with the Rabobank OmniKassa API.
 */
class StatusRequest extends AbstractRabobankRequest
{

    /**
     * @return string
     */
    public function getNotificationToken()
    {
        return $this->getParameter('notificationToken');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setNotificationToken($value)
    {
        return $this->setParameter('notificationToken', $value);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('notificationToken');

        return [];
    }

    /**
     * @param array $data
     * @return ResponseInterface|StatusResponse
     * @throws \Omnipay\Rabobank\Exception\InvalidSignatureException
     */
    public function sendData($data)
    {
        $headers = [];
        $headers['Authorization'] = 'Bearer '.$this->getNotificationToken();
        $response = $this->sendRequest(
            self::GET,
            'order/server/api/events/results/merchant.order.status.changed',
            $data,
            $headers
        );

        return $this->response = new StatusResponse($this, $response);
    }
}
