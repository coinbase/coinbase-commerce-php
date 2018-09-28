<?php
namespace CoinbaseCommerce\Resources\Operations;

use CoinbaseCommerce\Util;

trait CreateMethodTrait
{
    public function insert($headers = [])
    {
        $body = $this->getAttributes();
        $client = static::getClient();
        $path = static::getResourcePath();
        $response = $client->post($path, $body, $headers);
        $this->refreshFrom($response);
    }

    public static function create($body, $headers = [])
    {
        $client = static::getClient();
        $path = static::getResourcePath();
        $response = $client->post($path, $body, $headers);
        return Util::convertToApiObject($response);
    }
}
