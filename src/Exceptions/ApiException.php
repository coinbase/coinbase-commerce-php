<?php
namespace CoinbaseCommerce\Exceptions;

class ApiException extends CoinbaseException
{
    private $request;

    private $response;

    public function __construct($message, $request, $response, $previous)
    {
        parent::__construct($message, $previous->getCode(), $previous);

        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
}
