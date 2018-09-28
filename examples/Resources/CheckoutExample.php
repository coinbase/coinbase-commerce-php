<?php
require_once __DIR__ . "/vendor/autoload.php";

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Checkout;

/**
 * Init ApiClient with your Api Key
 * Your Api Keys are available in the Coinbase Commerce Dashboard.
 * Make sure you don't store your API Key in your source code!
 */
ApiClient::init("API_KEY");

$checkoutObj = new Checkout([
    "description" => "Mastering the Transition to the Information Age",
    "local_price" => [
        "amount" => "1.00",
        "currency" => "USD"
    ],
    "name" => "test item 15 edited",
    "pricing_type" => "fixed_price",
    "requested_info" => ["email"]
]);

try {
    $checkoutObj->save();
    echo sprintf("Successfully created new checkout with id: %s \n", $checkoutObj->id);
} catch (\Exception $exception) {
    echo sprintf("Enable to create checkout. Error: %s \n", $exception->getMessage());
}

if ($checkoutObj->id) {

    $checkoutObj->name = "New name";

    // Update "name"
    try {
        $checkoutObj->save();
        echo sprintf("Successfully updated name of checkout via save method\n");
    } catch (\Exception $exception) {
        echo sprintf("Enable to update name of checkout. Error: %s \n", $exception->getMessage());
    }

    // Update "name" by "id"
    try {
        Checkout::updateById(
            $checkoutObj->id,
            [
                "name" => "Another Name"
            ]
        );
        echo sprintf("Successfully updated name of checkout by id\n");
    } catch (\Exception $exception) {
        echo sprintf("Enable to update name of checkout by id. Error: %s \n", $exception->getMessage());
    }


    $checkoutObj->description = "New description";

    // Refresh attributes to previous values
    try {
        $checkoutObj->refresh();
        echo sprintf("Successfully refreshed checkout\n");
    } catch (\Exception $exception) {
        echo sprintf("Enable to refresh checkout. Error: %s \n", $exception->getMessage());
    }

    // Retrieve checkout by "id"
    try {
        $retrievedCheckout = Checkout::retrieve($checkoutObj->id);
        echo sprintf("Successfully retrieved checkout\n");
        echo $retrievedCheckout;
    } catch (\Exception $exception) {
        echo sprintf("Enable to retrieve checkout. Error: %s \n", $exception->getMessage());
    }
}

try {
    $list = Checkout::getList(["limit" => 5]);
    echo sprintf("Successfully got list of checkouts\n");

    if (count($list)) {
        echo sprintf("Checkouts in list:\n");

        foreach ($list as $checkout) {
            echo $checkout;
        }
    }

    echo sprintf("List's pagination:\n");
    print_r($list->getPagination());

    echo sprintf("Number of all checkouts - %s \n", $list->countAll());
} catch (\Exception $exception) {
    echo sprintf("Enable to get list of checkouts. Error: %s \n", $exception->getMessage());
}

if (isset($list) && $list->hasNext()) {
    // Load next page with previous settings (limit=5)
    try {
        $list->loadNext();
        echo sprintf("Next page of checkouts: \n");
        foreach ($list as $checkout) {
            echo $checkout;
        }
    } catch (\Exception $exception) {
        echo sprintf("Enable to get new page of checkouts. Error: %s \n", $exception->getMessage());
    }
}

try {
    $allCharge = Checkout::getAll();
    echo sprintf("Successfully got all checkouts:\n");
    foreach ($allCharge as $charge) {
        echo $charge;
    }
} catch (\Exception $exception) {
    echo sprintf("Enable to get all checkouts. Error: %s \n", $exception->getMessage());
}
