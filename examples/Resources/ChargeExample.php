<?php
require_once __DIR__ . "/vendor/autoload.php";

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;

/**
 * Init ApiClient with your Api Key
 * Your Api Keys are available in the Coinbase Commerce Dashboard.
 * Make sure you don't store your API Key in your source code!
 */
ApiClient::init("API_KEY");

$chargeObj = new Charge(
    [
        "description" => "Mastering the Transition to the Information Age",
        "metadata" => [
            "customer_id" => "id_1005",
            "customer_name" => "Satoshi Nakamoto"
        ],
        "name" => "Test Name",
        "payments" => [],
        "pricing_type" => "no_price"
    ]
);

try {
    $chargeObj->save();
    echo sprintf("Successfully created new charge with id: %s \n", $chargeObj->id);
} catch (\Exception $exception) {
    echo sprintf("Enable to create charge. Error: %s \n", $exception->getMessage());
}

if ($chargeObj->id) {
    $chargeObj->description = "New description";
    // Refresh attributes to previous values
    try {
        $chargeObj->refresh();
        echo sprintf("Successfully refreshed checkout.\n");
    } catch (\Exception $exception) {
        echo sprintf("Enable to refresh checkout. Error: %s \n", $exception->getMessage());
    }

    // Retrieve charge by "id"
    try {
        $retrievedCharge = Charge::retrieve($chargeObj->id);
        echo sprintf("Successfully retrieved charge\n");
        echo $retrievedCharge;
    } catch (\Exception $exception) {
        echo sprintf("Enable to retrieve charge. Error: %s \n", $exception->getMessage());
    }
}

try {
    $list = Charge::getList(["limit" => 5]);
    echo sprintf("Successfully got list of charges\n");

    if (count($list)) {
        echo sprintf("Charges in list:\n");

        foreach ($list as $charge) {
            echo $charge;
        }
    }

    echo sprintf("List's pagination:\n");
    print_r($list->getPagination());

    echo sprintf("Number of all charges - %s \n", $list->countAll());
} catch (\Exception $exception) {
    echo sprintf("Enable to get list of charges. Error: %s \n", $exception->getMessage());
}

if (isset($list) && $list->hasNext()) {
    // Load next page with previous settings (limit=5)
    try {
        $list->loadNext();
        echo sprintf("Next page of charges: \n");
        foreach ($list as $charge) {
            echo $charge;
        }
    } catch (\Exception $exception) {
        echo sprintf("Enable to get new page of charges. Error: %s \n", $exception->getMessage());
    }
}

// Load all avaialbe charges
try {
    $allCharge = Charge::getAll();
    echo sprintf("Successfully got all charges:\n");
    foreach ($allCharge as $charge) {
        echo $charge;
    }
} catch (\Exception $exception) {
    echo sprintf("Enable to get all charges. Error: %s \n", $exception->getMessage());
}
