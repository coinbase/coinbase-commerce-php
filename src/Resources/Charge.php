<?php
namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\Resources\Operations\CreateMethodTrait;
use CoinbaseCommerce\Resources\Operations\ReadMethodTrait;
use CoinbaseCommerce\Resources\Operations\SaveMethodTrait;
use CoinbaseCommerce\Util;

class Charge extends ApiResource implements ResourcePathInterface
{
    use CreateMethodTrait, ReadMethodTrait, SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'charges';
    }

    public function resolve($headers = [])
    {
        $id = $this->id;
        $path = Util::joinPath(static::getResourcePath(), $id, 'resolve');
        $client = static::getClient();
        $response = $client->post($path, [], $headers);
        $this->refreshFrom($response);
    }

    public function cancel($headers = [])
    {
        $id = $this->id;
        $path = Util::joinPath(static::getResourcePath(), $id, 'cancel');
        $client = static::getClient();
        $response = $client->post($path, [], $headers);
        $this->refreshFrom($response);
    }
}
