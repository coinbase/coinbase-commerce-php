<?php
namespace CoinbaseCommerce\Tests\GuzzleClientMock;

use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;

class OldGuzzleHelperHelper extends GuzzleHelperAbstract
{
    protected $client;

    protected $mock;

    protected $container = [];

    protected $history;

    public function __construct()
    {
        $this->client = new Client();
        $this->mock = new Mock();
        $this->history = new History();

        $this->client->getEmitter()->attach($this->mock);
        $this->client->getEmitter()->attach($this->history);
    }

    public function appendRequest($statusCode, $body, $headers = [])
    {
        $this->mock->addResponse(new Response($statusCode, $headers, Stream::factory(json_encode($body))));
    }

    private function loadTransactionRequests()
    {
        $newRequests = $this->history->getRequests();

        if (!empty($newRequests)) {
            $this->container = array_merge($this->container, $this->history->getRequests());
            $this->history->clear();
        }
    }

    public function shiftTransactionRequest()
    {
        $this->loadTransactionRequests();
        $request = array_shift($this->container);

        return [
            'params' => (string)$request->getQuery(),
            'method' => $request->getMethod(),
            'path' => $request->getPath()
        ];
    }
}
