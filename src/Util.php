<?php
namespace CoinbaseCommerce;

use CoinbaseCommerce\Resources\Charge;
use CoinbaseCommerce\Resources\Checkout;
use CoinbaseCommerce\Resources\Event;

class Util
{
    private static array $mapResourceByName = [];

    public static function convertToApiObject(mixed $response): mixed
    {
        if ($response instanceof ApiResponse) {
            $response = $response->bodyArray['data'] ?? null;
        }

        if (is_array($response)) {
            array_walk(
                $response,
                function (&$item) {
                    $item = self::convertToApiObject($item);
                }
            );
        }

        if (isset($response['resource']) && $resourceClass = self::getResourceClassByName($response['resource'])) {
            return new $resourceClass($response);
        }

        return $response;
    }

    public static function getResourceClassByName($name): mixed
    {
        if (empty(self::$mapResourceByName)) {
            self::$mapResourceByName = [
                'checkout' => Checkout::getClassName(),
                'charge' => Charge::getClassName(),
                'event' => Event::getClassName()
            ];
        }

        return self::$mapResourceByName[$name] ?? null;
    }

    public static function joinPath(): string
    {
        $arguments = func_get_args();
        array_walk(
            $arguments,
            function (&$item) {
                $item = trim($item, '/');
            }
        );

        return implode('/', $arguments);
    }

    public static function equal(mixed $prop1, mixed $prop2): bool
    {
        if (is_array($prop1)) {
            foreach ($prop1 as $key => $value) {
                if (!is_array($prop2) || !array_key_exists($key, $prop2)) {
                    return false;
                }
                if (is_array($value)) {
                    if (!self::equal($value, $prop2[$key])) {
                        return false;
                    }
                }
                if ($value != $prop2[$key]) {
                    return false;
                }
            }
        }

        return $prop1 === $prop2;
    }

    public static function hashEqual(string $str1, string $str2): bool
    {
        if (function_exists('hash_equals')) {
            return \hash_equals($str1, $str2);
        }

        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;

            for ($i = strlen($res) - 1; $i >= 0; $i--) {
                $ret |= ord($res[$i]);
            }
            return !$ret;
        }
    }
}
