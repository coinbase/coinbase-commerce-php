<?php
namespace CoinbaseCommerce;

class ApiResourceList extends \ArrayObject
{
    const CURSOR_PARAM = 'cursor_range';
    const PREV_CURSOR = 'ending_before';
    const NEXT_CURSOR = 'starting_after';

    private static ApiClient $apiClient;

    protected array $items = [];
    protected array $pagination = [];
    protected mixed $resourceClass;
    protected array $headers = [];
    protected array $params = [];

    public function __construct(mixed $resourceClass, array $items, array $pagination, array $params, array $headers)
    {
        parent::__construct();

        $this->resourceClass = $resourceClass;
        $this->items = $items;
        $this->pagination = $pagination;
        $this->params = $params;
        $this->headers = $headers;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function setPagination(array $pagination): void
    {
        $this->pagination = $pagination;
    }

    public function getPagination(): array
    {
        return $this->pagination;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasNext(): bool
    {
        return isset($this->pagination[self::CURSOR_PARAM][1]) && null !== $this->pagination[self::CURSOR_PARAM][1];
    }

    public function loadNext(): bool
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

    public function hasPrev(): bool
    {
        return isset($this->pagination[self::CURSOR_PARAM][0]) && null !== $this->pagination[self::CURSOR_PARAM][0];
    }

    public function loadPrev(): bool
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

    protected function loadPage($params): void
    {
        $client = self::getClient();
        $resourceClass = $this->resourceClass;
        $path = $resourceClass::getResourcePath();

        $response = $client->get($path, $params, $this->headers);
        $responseData = $response->bodyArray;

        $this->pagination = $responseData['pagination'] ?? [];
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

    public function offsetGet(mixed $key): mixed
    {
        return $this->items[$key];
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        null === $key ? array_push($this->items, $value) : $this->items[$key] = $value;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function countAll(): mixed
    {
        if (isset($this->pagination['total'])) {
            return $this->pagination['total'];
        }
    }

    public function asort(int $flags = SORT_REGULAR): bool
    {
        return asort($this->items, $flags);
    }

    public function ksort(int $flags = SORT_REGULAR): bool
    {
        return ksort($this->items, $flags);
    }

    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @throws \Exception
     */
    public static function getClient(): ApiClient
    {
        if (self::$apiClient) {
            return self::$apiClient;
        }
        return ApiClient::getInstance();
    }

    public static function setClient($client): void
    {
        self::$apiClient = $client;
    }

    public static function getClassName(): string
    {
        return get_called_class();
    }
}
