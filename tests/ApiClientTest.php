<?php
namespace CoinbaseCommerce\Tests;

use CoinbaseCommerce\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Please init client first.
     */
    public function testFailOnGetInstanceWithoutInit()
    {
        ApiClient::getInstance();
    }

    public function testInitWithParams()
    {
        $apiKey = 'test_api_key';
        $baseApiUrl = 'http://test.com/';
        $apiVersion = '2018-03-20';
        $timeout = 5;

        $client = ApiClient::init($apiKey, $baseApiUrl, $apiVersion, $timeout);

        $this->assertEquals($apiKey, $client->getApiKey());
        $this->assertEquals($baseApiUrl, $client->getBaseUrl());
        $this->assertEquals($apiVersion, $client->getApiVersion());
        $this->assertEquals($timeout, $client->getTimeout());
    }

    public function testCorrectReinit()
    {
        $apiKey = 'test_api_key';
        $baseApiUrl = 'http://test.com/';
        $apiVersion = '2018-03-20';
        $timeout = 5;

        $clientFirst = ApiClient::init($apiKey, $baseApiUrl, $apiVersion, $timeout);

        $apiKey = 'another_test_api_key';

        $clientSecond = ApiClient::init($apiKey, $baseApiUrl, $apiVersion, $timeout);

        $this->assertSame($clientFirst, $clientSecond);
        $this->assertInstanceOf(ApiClient::getClassName(), $clientSecond);
    }
}
