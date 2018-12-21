<?php
namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\Resources\Operations\ReadMethodTrait;

class Event extends ApiResource implements ResourcePathInterface
{
    use ReadMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'events';
    }
}
