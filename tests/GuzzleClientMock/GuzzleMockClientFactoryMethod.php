<?php
namespace CoinbaseCommerce\Tests\GuzzleClientMock;

class GuzzleMockClientFactoryMethod
{
    public static function create()
    {
        if (class_exists('GuzzleHttp\Handler\MockHandler')) {
            return new NewGuzzleHelperHelper();
        } elseif (class_exists('GuzzleHttp\Subscriber\Mock')) {
            return new OldGuzzleHelperHelper();
        } else {
            throw new \Exception('Not supported Guzzle version.');
        }
    }
}
