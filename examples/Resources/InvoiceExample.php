<?php
require_once __DIR__ . "/vendor/autoload.php";

use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Invoice;

/**
 * Init ApiClient with your Api Key
 * Your Api Keys are available in the Coinbase Commerce Dashboard.
 * Make sure you don't store your API Key in your source code!
 */
ApiClient::init("API_KEY");

$invoiceObj = new Invoice(
	[
	    'business_name' => 'Crypto Account LLC',
	    'customer_email' => 'customer@test.com',
	    'customer_name' => 'Test Customer',
	    'local_price' => [
	        'amount' => '100.00',
	        'currency' => 'USD'
	    ],
	    'memo' => 'Taxes and Accounting Services'
	];
);

try {
    $invoiceObj->save();
    echo sprintf("Successfully created new charge with id: %s \n", $invoiceObj->id);
} catch (\Exception $exception) {
    echo sprintf("Enable to create charge. Error: %s \n", $exception->getMessage());
}

if ($invoiceObj->id) {
    // Retrieve charge by "id"
    try {
        $retrievedInvoice = Invoice::retrieve($invoiceObj->id);
        echo sprintf("Successfully retrieved charge\n");
        echo $retrievedInvoice;
    } catch (\Exception $exception) {
        echo sprintf("Enable to retrieve charge. Error: %s \n", $exception->getMessage());
    }
}

try {
    $list = Invoice::getList(["limit" => 5]);
    echo sprintf("Successfully got list of charges\n");

    if (count($list)) {
        echo sprintf("Invoices in list:\n");

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
    $allInvoice = Invoice::getAll();
    echo sprintf("Successfully got all charges:\n");
    foreach ($allInvoice as $charge) {
        echo $charge;
    }
} catch (\Exception $exception) {
    echo sprintf("Enable to get all charges. Error: %s \n", $exception->getMessage());
}
