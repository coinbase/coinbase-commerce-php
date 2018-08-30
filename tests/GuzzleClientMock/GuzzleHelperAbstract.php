<?php
namespace CoinbaseCommerce\Tests\GuzzleClientMock;

abstract class GuzzleHelperAbstract
{
    protected $client;

    abstract public function appendRequest($statusCode, $body, $headers = []);

    abstract public function shiftTransactionRequest();

    public function getClient()
    {
        return $this->client;
    }
}
