<?php
namespace CoinbaseCommerce\Resources;

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\ApiResponse;
use CoinbaseCommerce\Util;

class ApiResource extends \ArrayObject
{
    protected static $client;

    protected $attributes = [];

    protected $initialData = [];

    public function __construct($data = [])
    {
        $data = $data ?: [];
        $this->refreshFrom($data);
    }

    protected function refreshFrom($data)
    {
        $this->clearAttributes();

        if ($data instanceof ApiResponse) {
            $data = isset($data->bodyArray['data']) ? $data->bodyArray['data'] : null;
        }

        foreach ($data as $key => $value) {
            $value = Util::convertToApiObject($value);
            $this->attributes[$key] = is_array($value) ? new \ArrayObject($value) : $value;
            $this->initialData[$key] = $value;
        }
    }

    protected function clearAttributes()
    {
        $this->attributes = [];
        $this->initialData = [];
    }

    public static function getPrimaryKeyName()
    {
        return 'id';
    }

    public static function getClassName()
    {
        return get_called_class();
    }

    public function getPrimaryKeyValue()
    {
        return isset($this->attributes[static::getPrimaryKeyName()]) ? $this->attributes[static::getPrimaryKeyName()] : null;
    }

    public function __set($key, $value)
    {
        if (\is_string($key)) {
            $this->attributes[$key] = is_array($value) ? new \ArrayObject($value) : $value;
        }
    }

    public function __get($key)
    {
        if (\is_string($key) && isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function getAttributes()
    {
        $returnAttributes = [];

        foreach ($this->attributes as $key => $attribute) {
            $returnAttributes[$key] = $attribute instanceof \ArrayObject ? $attribute->getArrayCopy() : $attribute;
        }

        return $returnAttributes;
    }

    public function getAttribute($key)
    {
        $attribute = $this->__get($key);

        return $attribute instanceof \ArrayObject ? $attribute->getArrayCopy() : $attribute;
    }

    public function getDirtyAttributes()
    {
        $dirtyAttributes = [];

        foreach ($this->attributes as $key => $value) {
            $value = $value instanceof \ArrayObject ? $value->getArrayCopy() : $value;

            if (isset($this->initialData[$key])) {
                $initialValue = $this->initialData[$key] instanceof \ArrayObject ? $this->initialData[$key]->getArrayCopy() : $this->initialData[$key];
                if (!Util::equal($value, $initialValue)) {
                    $dirtyAttributes[$key] = $value;
                }
            } else {
                $dirtyAttributes[$key] = $value;
            }
        }

        return $dirtyAttributes;
    }

    public function __toString()
    {
        return print_r($this->attributes, true);
    }

    public static function setClient($client)
    {
        self::$client = $client;
    }

    protected static function getClient()
    {
        if (self::$client) {
            return self::$client;
        }

        return ApiClient::getInstance();
    }

    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    public function offsetSet($key, $value)
    {
        null === $key ? array_push($this->attributes, $value) : $this->attributes[$key] = $value;
    }

    public function count()
    {
        return count($this->attributes);
    }

    public function asort()
    {
        asort($this->attributes);
    }

    public function ksort()
    {
        ksort($this->attributes);
    }

    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
}
