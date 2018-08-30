<?php
namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\Resources\Operations\CreateMethodTrait;
use CoinbaseCommerce\Resources\Operations\ReadMethodTrait;
use CoinbaseCommerce\Resources\Operations\SaveMethodTrait;

class Charge extends ApiResource
{
    use CreateMethodTrait, ReadMethodTrait, SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'charges';
    }
}
