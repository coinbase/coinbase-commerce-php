<?php
namespace CoinbaseCommerce;

class ApiResourceList extends \ArrayObject
{
    const CURSOR_PARAM = 'cursor_range';
    const PREV_CURSOR = 'ending_before';
    const NEXT_CURSOR = 'starting_after';

    private static $apiClient;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $pagination = [];

    /**
     * @var string
     */
    protected $resourceClass;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * ApiResourceList constructor.
     * @param array $items
     * @param array $pagination
     */
    public function __construct($resourceClass, $items, $pagination, $params, $headers)
    {
        $this->resourceClass = $resourceClass;
        $this->items = $items;
        $this->pagination = $pagination;
        $this->params = $params;
        $this->headers = $headers;
    }

    /**
     * @param $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @param $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return isset($this->pagination[self::CURSOR_PARAM][1]) && null !== $this->pagination[self::CURSOR_PARAM][1];
    }

    /**
     * @return bool
     */
    public function loadNext()
    {
        if (!$this->hasNext()) {
            return false;
        }

        $nextCursor = $this->pagination[self::CURSOR_PARAM][1];
        $params = $this->getParams();
        $params[self::NEXT_CURSOR] = $nextCursor;
        $this->loadPage($params);

        return true;
    }

    /**
     * @return bool
     */
    public function hasPrev()
    {
        return isset($this->pagination[self::CURSOR_PARAM][0]) && null !== $this->pagination[self::CURSOR_PARAM][0];
    }

    /**
     * @return bool
     */
    public function loadPrev()
    {
        if (!$this->hasPrev()) {
            return false;
        }

        $prevCursor = $this->pagination[self::CURSOR_PARAM][0];
        $params = $this->getParams();
        $params[self::PREV_CURSOR] = $prevCursor;
        $this->loadPage($params);

        return true;
    }

    protected function loadPage($params)
    {
        $client = self::getClient();
        $resourceClass = $this->resourceClass;
        $path = $resourceClass::getResourcePath();

        $response = $client->get($path, $params, $this->headers);
        $responseData = $response->bodyArray;

        $this->pagination = isset($responseData['pagination']) ? $responseData['pagination'] : [];
        $this->items = [];

        if (isset($responseData['data'])) {
            $this->items = array_map(
                function ($item) {
                    return Util::convertToApiObject($item);
                },
                $responseData['data']
            );
        }
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public function offsetSet($key, $value)
    {
        null === $key ? array_push($this->items, $value) : $this->items[$key] = $value;
    }

    public function count()
    {
        return count($this->items);
    }

    public function countAll()
    {
        if (isset($this->pagination['total'])) {
            return $this->pagination['total'];
        }
    }

    public function asort()
    {
        asort($this->items);
    }

    public function ksort()
    {
        ksort($this->items);
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public static function getClient()
    {
        if (self::$apiClient) {
            return self::$apiClient;
        }
        return ApiClient::getInstance();
    }

    public function setClient($client)
    {
        self::$apiClient = $client;
    }

    public static function getClassName()
    {
        return get_called_class();
    }
}
