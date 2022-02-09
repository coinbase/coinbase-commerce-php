<?php

namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\Resources\Operations\CreateMethodTrait;
use CoinbaseCommerce\Resources\Operations\DeleteMethodTrait;
use CoinbaseCommerce\Resources\Operations\ReadMethodTrait;
use CoinbaseCommerce\Resources\Operations\SaveMethodTrait;
use CoinbaseCommerce\Resources\Operations\UpdateMethodTrait;

class Checkout extends ApiResource implements ResourcePathInterface
{
    use ReadMethodTrait;
    use CreateMethodTrait;
    use UpdateMethodTrait;
    use DeleteMethodTrait;
    use SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'checkouts';
    }
}
