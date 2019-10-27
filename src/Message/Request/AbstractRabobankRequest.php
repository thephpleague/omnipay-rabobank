<?php

namespace Omnipay\Rabobank\Message\Request;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Rabobank\Gateway;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * This class holds all the common things for all of Rabobank OmniKassa requests.
 */
abstract class AbstractRabobankRequest extends AbstractRequest
{
    const POST = 'POST';
    const GET = 'GET';

    protected static $accessToken;

    /**
     * @var string
     */
    protected $baseUrl = 'https://betalen.rabobank.nl/omnikassa-api/';

    /**
     * @var string
     */
    protected $baseUrlTesting = 'https://betalen.rabobank.nl/omnikassa-api-sandbox/';

    /**
     * @var string
     */
    protected $refreshEndpoint = 'gatekeeper/refresh';

    /**
     * @var string
     */
    protected $requestContentType = 'application/json';

    /**
     * @var Gateway
     */
    public $gateway;

    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest, Gateway $gateway)
    {
        parent::__construct($httpClient, $httpRequest);
        $this->gateway = $gateway;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        if (!isset(self::$accessToken)) {
            $this->setAccessToken($this->fetchAccessToken());
        }

        return self::$accessToken;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAccessToken($value)
    {
        self::$accessToken = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->gateway->getTestMode()) {
            return $this->baseUrlTesting;
        }

        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getRequestContentType()
    {
        return $this->requestContentType;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    protected function sendRequest($method, $endpoint, array $data = null, array $headers = [])
    {
        if (!isset($headers['Authorization'])) {
            $headers['Authorization'] = 'Bearer '.$this->getAccessToken();
        }

        $headers['Content-Type'] = $this->getRequestContentType();

        $response = $this->httpClient->request(
            $method,
            $this->getBaseUrl().$endpoint,
            $headers,
            ($data === null || $data === []) ? null : json_encode($data)
        );

        return json_decode((string)$response->getBody(), true);
    }

    protected function fetchAccessToken()
    {
        $data = $this->sendRequest(self::GET, $this->refreshEndpoint, null, [
            'Authorization' => 'Bearer '.$this->gateway->getRefreshToken(),
        ]);

        if (!isset($data['token'])) {
            throw new InvalidResponseException($data['consumerMessage'], $data['errorCode']);
        }
        
        return $data['token'];
    }

    protected function createAmountObject()
    {
        return [
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),
        ];
    }
}
