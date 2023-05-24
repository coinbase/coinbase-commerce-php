<?php
namespace CoinbaseCommerce;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiResponse
 * @package CoinbaseCommerce
 */
class ApiResponse
{
    const REQUEST_ID_HEADER = 'x-request-id';
    public array $headers;
    public string $body;
    public mixed $bodyArray;
    public int $code;
    public mixed $requestId;

    /**
     * ApiResponse constructor.
     */
    public function __construct(ResponseInterface $response)
    {
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
