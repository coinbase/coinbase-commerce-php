<?php
namespace CoinbaseCommerce;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class ApiClient
{
    const API_KEY_PARAM = 'apiKey';
    const BASE_API_URL_PARAM = 'baseApiUrl';
    const API_VERSION_PARAM = 'apiVersion';
    const TIMEOUT_PARAM = 'timeout';

    /**
     * @var array
     */
    private $params = [
        self::API_VERSION_PARAM => null,
        self::BASE_API_URL_PARAM => 'https://api.commerce.coinbase.com/',
        self::API_VERSION_PARAM => '2018-03-22',
        self::TIMEOUT_PARAM => 3
    ];

    /**
     * @var ApiClient
     */
    private static $instance;

    /**
     * @var
     */
    private $logger;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var
     */
    private $httpClient;

    /**
     * ApiClient constructor.
     */
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param string $apiKey
     * @param null|string $baseUrl
     * @param null|string $apiVersion
     * @param null|integer $timeout
     * @return ApiClient
     */
    public static function init($apiKey, $baseUrl = null, $apiVersion = null, $timeout = null)
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        self::$instance->setApiKey($apiKey)
            ->setBaseUrl($baseUrl)
            ->setApiVersion($apiVersion)
            ->setTimeout($timeout);

        return self::$instance;
    }

    /**
     * @return ApiClient
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            throw new \Exception('Please init client first.');
        }

        return self::$instance;
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getParam($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    private function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     * @throws \Exception
     */
    public function setApiKey($value)
    {
        if (empty($value)) {
            throw new \Exception('Api Key is required');
        }

        $this->setParam(self::API_KEY_PARAM, $value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getParam(self::API_KEY_PARAM);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBaseUrl($value)
    {
        if (!empty($value) && \is_string($value)) {
            if (substr($value, -1) !== '/') {
                $value .= '/';
            }

            $this->setParam(self::BASE_API_URL_PARAM, $value);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->getParam(self::BASE_API_URL_PARAM);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiVersion($value)
    {
        if (!empty($value) && \is_string($value)) {
            $this->setParam(self::API_VERSION_PARAM, $value);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->getParam(self::API_VERSION_PARAM);
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setTimeout($value)
    {
        if (!empty($value) && \is_numeric($value)) {
            $this->setParam(self::TIMEOUT_PARAM, $value);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->getParam(self::TIMEOUT_PARAM);
    }

    /**
     * @param array $query
     * @param array $body
     * @param array $headers
     * @return array
     */
    private function generateRequestOptions($query = [], $body = [], $headers = [])
    {
        return $options = [
            'headers' => array_merge(
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Coinbase ',
                    'X-CC-Api-Key' => $this->getParam('apiKey'),
                    'X-CC-Version' => $this->getParam('apiVersion')
                ],
                $headers
            ),
            'query' => $query,
            'json' => $body,
            'timeout' => $this->getParam('timeout')
        ];
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $options
     * @return ApiResponse
     */
    private function makeRequest($method, $path, $options)
    {
        try {
            $path = Util::joinPath($this->getParam('baseApiUrl'), $path);
            $client = $this->getHttpClient();

            if (method_exists($client, 'createRequest')) {
                $request = $client->createRequest($method, $path, $options);
                $response = $client->send($request);
            } else {
                $response = $client->request($method, $path, $options);
            }
            $apiResponse = new ApiResponse($response);
            $this->setLastResponse($apiResponse);
            $this->logWarnings($apiResponse);

            return $apiResponse;
        } catch (RequestException $exception) {
            throw ApiErrorFactory::create($exception);
        }
    }

    public function getHttpClient()
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = new Client();
        }

        return $this->httpClient;
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $path
     * @param array $queryParams
     * @param array $headers
     * @return ApiResponse
     */
    public function get($path, $queryParams = [], $headers = [])
    {
        $options = $this->generateRequestOptions($queryParams, $headers);
        return $this->makeRequest('GET', $path, $options);
    }

    /**
     * @param string $path
     * @param array $body
     * @param array $headers
     * @return ApiResponse
     */
    public function post($path, $body, $headers)
    {
        $options = $this->generateRequestOptions([], $body, $headers = []);
        return $this->makeRequest('POST', $path, $options);
    }

    /**
     * @param string $path
     * @param array $headers
     * @return ApiResponse
     */
    public function put($path, $body, $headers)
    {
        $options = $this->generateRequestOptions([], $body, $headers = []);
        return $this->makeRequest('PUT', $path, $options);
    }

    /**
     * @param string $path
     * @param array $headers
     * @return ApiResponse
     */
    public function delete($path, $headers = [])
    {
        $options = $this->generateRequestOptions([], [], $headers);
        return $this->makeRequest('DELETE', $path, $options);
    }

    /**
     * @param ApiResponse $response
     */
    public function setLastResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return ApiResponse
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    /**
     * @param ApiResponse $response
     */
    public function logWarnings($response)
    {
        if (!$this->logger) {
            return;
        }

        $data = $response->bodyArray;

        if (!isset($data['warnings'])) {
            return;
        }

        foreach ($data['warnings'] as $warning) {
            $this->logger->warning(is_array($warning) ? implode(',', $warning) : $warning);
        }
    }

    public static function getClassName()
    {
        return get_called_class();
    }
}
