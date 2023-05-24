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
    private static array $mapErrorMessageToClass = [];
    private static array $mapErrorCodeToClass = [];

    public static function getErrorClassByMessage(mixed $message): mixed
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

        return self::$mapErrorMessageToClass[$message] ?? null;
    }

    public static function getErrorClassByCode(int $code): mixed
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

        return self::$mapErrorCodeToClass[$code] ?? null;
    }

    public static function create(RequestException $exception): mixed
    {
        $response = $exception->getResponse();
        $request = $exception->getRequest();
        $code = $exception->getCode();
        $data = $response ? json_decode($response->getBody(), true) : null;
        $errorMessage = $data['error']['message'] ?? $exception->getMessage();
        $errorId = $data['error']['type'] ?? null;

        $errorClass = self::getErrorClassByMessage($errorId) ?: self::getErrorClassByCode($code) ?: ApiException::getClassName();

        return new $errorClass($errorMessage, $request, $response, $exception);
    }
}
