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

    private static ApiClient $instance;

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

    private bool $verifySSL = true ;

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
     * @throws \Exception
     */
    public static function init(string $apiKey, ?string $baseUrl = null, ?string $apiVersion = null, ?int $timeout = null): ApiClient
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
     * @throws \Exception
     */
    public static function getInstance(): ApiClient
    {
        if (!self::$instance) {
            throw new \Exception('Please init client first.');
        }

        return self::$instance;
    }

    private function getParam(string $key): mixed
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
    }

    private function setParam(string $key,  mixed $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setApiKey(string $value): self
    {
        if (empty($value)) {
            throw new \Exception('Api Key is required');
        }

        $this->setParam(self::API_KEY_PARAM, $value);
        return $this;
    }

    public function getApiKey(): mixed
    {
        return $this->getParam(self::API_KEY_PARAM);
    }

    public function setBaseUrl(string $value): self
    {
        if (!empty($value) && \is_string($value)) {
            if (substr($value, -1) !== '/') {
                $value .= '/';
            }

            $this->setParam(self::BASE_API_URL_PARAM, $value);
        }

        return $this;
    }

    public function getBaseUrl(): mixed
    {
        return $this->getParam(self::BASE_API_URL_PARAM);
    }

    public function setApiVersion(string $value): self
    {
        if (!empty($value)) {
            $this->setParam(self::API_VERSION_PARAM, $value);
        }

        return $this;
    }

    public function getApiVersion(): mixed
    {
        return $this->getParam(self::API_VERSION_PARAM);
    }

    public function setTimeout(int $value): self
    {
        if (!empty($value)) {
            $this->setParam(self::TIMEOUT_PARAM, $value);
        }

        return $this;
    }

    public function getTimeout(): mixed
    {
        return $this->getParam(self::TIMEOUT_PARAM);
    }

    private function generateRequestOptions(array $query = [], array $body = [], array $headers = []): array
    {
        return [
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

    private function makeRequest(string $method, string $path, array $options): ApiResponse
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

    public function getHttpClient(): Client
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = new Client(['verify' => $this->verifySSL]);
        }

        return $this->httpClient;
    }

    public function setLogger($logger): void
    {
        $this->logger = $logger;
    }

    public function get(string $path, array $queryParams = [], array $headers = []): ApiResponse
    {
        $options = $this->generateRequestOptions($queryParams, $headers);
        return $this->makeRequest('GET', $path, $options);
    }

    public function post(string $path, array $body, array $headers): ApiResponse
    {
        $options = $this->generateRequestOptions([], $body, $headers = []);
        return $this->makeRequest('POST', $path, $options);
    }

    public function put(string $path, array $body, array $headers): ApiResponse
    {
        $options = $this->generateRequestOptions([], $body, $headers = []);
        return $this->makeRequest('PUT', $path, $options);
    }

    public function delete(string $path, array $headers = []): ApiResponse
    {
        $options = $this->generateRequestOptions([], [], $headers);
        return $this->makeRequest('DELETE', $path, $options);
    }

    public function setLastResponse(ApiResponse $response): void
    {
        $this->response = $response;
    }

    public function getLastResponse(): ApiResponse
    {
        return $this->response;
    }

    public function logWarnings(ApiResponse $response): void
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

    public static function getClassName(): string
    {
        return get_called_class();
    }

    public function verifySsl($verify): void
    {
        if(!is_bool($verify)) {
           return;
        }
        $this->verifySSL = $verify ;
    }
}
