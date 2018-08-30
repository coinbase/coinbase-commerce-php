<?php
namespace CoinbaseCommerce\Resources\Operations;

use CoinbaseCommerce\Util;
use CoinbaseCommerce\ApiResourceList;

trait ReadMethodTrait
{
    public static function retrieve($id, $headers = [])
    {
        if (!\is_scalar($id)) {
            throw new \Exception('Invalid id provided.');
        }

        $client = static::getClient();
        $path = Util::joinPath(static::getResourcePath(), $id);
        $responseData = $client->get($path, [], $headers);

        return Util::convertToApiObject($responseData);
    }

    public function refresh($headers = [])
    {
        $id = $this->getPrimaryKeyValue();

        if (!\is_scalar($id)) {
            throw new \Exception('Invalid id provided');
        }

        $client = static::getClient();
        $path = Util::joinPath(static::getResourcePath(), $id);
        $response = $client->get($path, [], $headers);

        $this->refreshFrom($response);
    }

    public static function getList($params = [], $headers = [])
    {
        $path = static::getResourcePath();
        $client = static::getClient();
        $response = $client->get($path, $params, $headers);
        $responseData = $response->bodyArray;
        $pagination = isset($responseData['pagination']) ? $responseData['pagination'] : [];
        $items = [];

        if (isset($responseData['data'])) {
            $items = array_map(
                function ($item) {
                    return Util::convertToApiObject($item);
                },
                $responseData['data']
            );
        }

        return new ApiResourceList(self::getClassName(), $items, $pagination, $params, $headers);
    }

    public static function getAll($params = [], $headers = [])
    {
        $list = [];
        $path = static::getResourcePath();
        $client = static::getClient();

        $loadPage = function ($params, &$list) use (&$loadPage, $client, $path, $headers) {

            $response = $client->get($path, $params, $headers);
            $responseData = $response->bodyArray;
            $items = array_map(
                function ($item) {
                    return Util::convertToApiObject($item);
                },
                $responseData['data']
            );

            $pagination = $responseData['pagination'];
            $shown = $pagination['yielded'] ? : 0;
            $limit = $pagination['limit'] ? : 0;
            $cursorRange = $pagination['cursor_range'] ? : [];

            $list = array_merge($list, $items);

            if ($shown < $limit) {
                return;
            }

            if (is_array($cursorRange) && count($cursorRange)) {
                $params['starting_after'] = end($cursorRange);
            } else {
                return;
            }

            $loadPage($params, $list);
        };

        $loadPage($params, $list);

        return $list;
    }
}
