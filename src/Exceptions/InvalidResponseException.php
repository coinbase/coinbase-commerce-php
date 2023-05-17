<?php
namespace CoinbaseCommerce\Exceptions;

class InvalidResponseException extends CoinbaseException
{
    /**
     * @var mixed|string
     */
    private mixed $body;

    public function __construct($message = '', $body = '')
    {
        parent::__construct($message);
        $this->body = $body;
    }
}
