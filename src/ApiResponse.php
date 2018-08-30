<?php
namespace CoinbaseCommerce;

/**
 * Class ApiResponse
 * @package CoinbaseCommerce
 */
class ApiResponse
{
    const REQUEST_ID_HEADER = 'x-request-id';
    /**
     * @var array
     */
    public $headers;
    /**
     * @var string
     */
    public $body;
    /**
     * @var mixed
     */
    public $bodyArray;
    /**
     * @var integer
     */
    public $code;
    /**
     * @var mixed|null
     */
    public $requestId;

    /**
     * ApiResponse constructor.
     * @param GuzzleHttp\Psr7\Response $response
     */
    public function __construct($response)
    {
        if ($response) {
            $this->code = $response->getStatusCode();
            $this->headers = $response->getHeaders();
            $this->body = (string)$response->getBody();
            $lowerCaseKeys = array_change_key_case($this->headers);
            $this->requestId = array_key_exists(strtolower(self::REQUEST_ID_HEADER), $lowerCaseKeys)
                && !empty($lowerCaseKeys[strtolower(self::REQUEST_ID_HEADER)][0]) ?
                $lowerCaseKeys[strtolower(self::REQUEST_ID_HEADER)][0] : null;

            $this->bodyArray = !empty($this->body)? \json_decode($this->body, true): null;
        }
    }
}
