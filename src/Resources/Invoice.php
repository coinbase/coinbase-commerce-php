<?php
namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\Resources\Operations\CreateMethodTrait;
use CoinbaseCommerce\Resources\Operations\ReadMethodTrait;
use CoinbaseCommerce\Resources\Operations\SaveMethodTrait;
use CoinbaseCommerce\Util;

class Invoice extends ApiResource implements ResourcePathInterface
{
    use ReadMethodTrait, CreateMethodTrait, SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'invoices';
    }

    public function void($headers = [])
    {
        $id = $this->id;
        $path = Util::joinPath(static::getResourcePath(), $id, 'void');
        $client = static::getClient();
        $response = $client->put($path, [], $headers);
        $this->refreshFrom($response);
    }

    public function resolve($headers = [])
    {
        $id = $this->id;
        $path = Util::joinPath(static::getResourcePath(), $id, 'resolve');
        $client = static::getClient();
        $response = $client->post($path, [], $headers);
        $this->refreshFrom($response);
    }
}
