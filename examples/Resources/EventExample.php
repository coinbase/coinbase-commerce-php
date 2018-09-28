<?php
require_once __DIR__ . "/vendor/autoload.php";

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Event;

/**
 * Init ApiClient with your Api Key
 * Your Api Keys are available in the Coinbase Commerce Dashboard.
 * Make sure you don't store your API Key in your source code!
 */
ApiClient::init("API_KEY");

try {
    $list = Event::getList(["limit" => 5]);
    echo sprintf("Successfully got list of events\n");

    if (count($list)) {
        echo sprintf("Events in list:\n");

        foreach ($list as $event) {
            echo $event;
        }
    }

    echo sprintf("List\"s pagination:\n");
    print_r($list->getPagination());

    echo sprintf("Number of all events - %s \n", $list->countAll());
} catch (\Exception $exception) {
    echo sprintf("Enable to get list of events. Error: %s \n", $exception->getMessage());
}

if ($list && $list->hasNext()) {
    // Load next page with previous settings (limit=5)
    try {
        $list->loadNext();
        echo sprintf("Next page of events: \n");
        foreach ($list as $event) {
            echo $event;
        }
    } catch (\Exception $exception) {
        echo sprintf("Enable to get new page of events. Error: %s \n", $exception->getMessage());
    }
}

// Load all events in array page by page
try {
    $allCharge = Event::getAll();
    echo sprintf("Successfully got all events:\n");
    foreach ($allCharge as $event) {
        echo $event;
    }
} catch (\Exception $exception) {
    echo sprintf("Enable to get all events. Error: %s \n", $exception->getMessage());
}
