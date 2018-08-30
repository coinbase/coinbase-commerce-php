<?php
namespace CoinbaseCommerce;

use CoinbaseCommerce\Exceptions\AuthenticationException;
use CoinbaseCommerce\Exceptions\InternalServerException;
use CoinbaseCommerce\Exceptions\InvalidRequestException;
use CoinbaseCommerce\Exceptions\ParamRequiredException;
use CoinbaseCommerce\Exceptions\RateLimitExceededException;
use CoinbaseCommerce\Exceptions\ResourceNotFoundException;
use CoinbaseCommerce\Exceptions\ServiceUnavailableException;
use CoinbaseCommerce\Exceptions\ValidationException;
use CoinbaseCommerce\Exceptions\ApiException;
use GuzzleHttp\Exception\RequestException;

class ApiErrorFactory
{
    /**
     * @var array
     */
    private static $mapErrorMessageToClass = [];

    /**
     * @var array
     */
    private static $mapErrorCodeToClass = [];

    /**
     * @param $message
     * @return mixed|null
     */
    public static function getErrorClassByMessage($message)
    {
        if (empty(self::$mapErrorMessageToClass)) {
            self::$mapErrorMessageToClass = [
                'not_found' => ResourceNotFoundException::getClassName(),
                'param_required' => ParamRequiredException::getClassName(),
                'validation_error' => ValidationException::getClassName(),
                'invalid_request' => InvalidRequestException::getClassName(),
                'authentication_error' => AuthenticationException::getClassName(),
                'rate_limit_exceeded' => RateLimitExceededException::getClassName(),
                'internal_server_error' => InternalServerException::getClassName()
            ];
        }

        return isset(self::$mapErrorMessageToClass[$message]) ? self::$mapErrorMessageToClass[$message]: null;
    }

    /**
     * @param $code
     * @return mixed|null
     */
    public static function getErrorClassByCode($code)
    {
        if (empty(self::$mapErrorCodeToClass)) {
            self::$mapErrorCodeToClass = [
                400 => InvalidRequestException::getClassName(),
                401 => AuthenticationException::getClassName(),
                404 => ResourceNotFoundException::getClassName(),
                429 => RateLimitExceededException::getClassName(),
                500 => InternalServerException::getClassName(),
                503 => ServiceUnavailableException::getClassName()
            ];
        }

        return isset(self::$mapErrorCodeToClass[$code]) ? self::$mapErrorCodeToClass[$code]: null;
    }

    /**
     * @param RequestException $exception
     */
    public static function create($exception)
    {
        $response = $exception->getResponse();
        $request = $exception->getRequest();
        $code = $exception->getCode();
        $data = $response ? json_decode($response->getBody(), true) : null;
        $errorMessage = isset($data['error']['message']) ? $data['error']['message'] : $exception->getMessage();
        $errorId = isset($data['error']['type']) ? $data['error']['type'] : null;

        $errorClass = self::getErrorClassByMessage($errorId) ?: self::getErrorClassByCode($code) ?: ApiException::getClassName();

        return new $errorClass($errorMessage, $request, $response, $exception);
    }
}
