<?php

namespace CoinbaseCommerce\Tests\Resources;

use CoinbaseCommerce\ApiResourceList;
use CoinbaseCommerce\Resources\Charge;
use CoinbaseCommerce\Resources\Checkout;
use CoinbaseCommerce\Resources\Event;
use CoinbaseCommerce\Tests\BaseTest;

class CheckoutTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        Checkout::setClient($this->apiClient);
    }

    public function testRefreshMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $checkoutObj = new Checkout();
        $checkoutObj->id = $id;
        $checkoutObj->refresh();

        $this->assertRequested('GET', '/checkouts/' . $id, '');
        $this->assertEquals('Test Description', $checkoutObj->description);
    }

    public function testRetrieveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $checkoutObj = Checkout::retrieve($id);

        $this->assertRequested('GET', '/checkouts/' . $id, '');
        $this->assertEquals('Test Description', $checkoutObj['description']);
        $this->assertEquals('Test Description', $checkoutObj->description);
    }

    public function testListMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkoutList.json'));
        $checkoutList = Checkout::getList(['limit' => 2]);

        $this->assertRequested('GET', '/checkouts', 'limit=2');
        $this->assertInstanceOf(ApiResourceList::getClassName(), $checkoutList);
        $this->assertEquals('Mastering the Transition to the Information Age', $checkoutList[0]['description']);
        $this->assertEquals('Mastering the Transition to the Information Age', $checkoutList[0]->description);
    }

    public function testInsertMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description'
        ];
        $checkoutObj = new Checkout($data);
        $checkoutObj->insert();

        $this->assertRequested('POST', '/checkouts', '');
        $this->assertEquals('Test Name', $checkoutObj->name);
    }

    public function testInsertSaveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $checkoutObj = new Checkout(
            [
                'name' => 'Test Name',
                'description' => 'Test description'
            ]
        );
        $checkoutObj->save();

        $this->assertRequested('POST', '/checkouts', '');
        $this->assertInstanceOf(Checkout::getClassName(), $checkoutObj);
        $this->assertEquals('Test Name', $checkoutObj->name);
    }

    public function testCreateMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $chargeObj = Checkout::create(
            [
                'name' => 'Test Name',
                'description' => 'Test description'
            ]
        );

        $this->assertRequested('POST', '/checkouts', '');
        $this->assertInstanceOf(Checkout::getClassName(), $chargeObj);
        $this->assertEquals('Test Name', $chargeObj->name);
    }

    public function testUpdateMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $checkoutObj = new Checkout(
            [
                'id' => $id,
                'name' => 'Test Name',
                'description' => 'Test description'
            ]
        );
        $checkoutObj->update();

        $this->assertRequested('PUT', '/checkouts/' . $id, '');
        $this->assertEquals('Test Description', $checkoutObj['description']);
        $this->assertEquals('Test Description', $checkoutObj->description);
    }

    public function testUpdateByIdMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $data = [
            'name' => 'Test Name',
            'description' => 'Test description'
        ];

        $checkoutObj = Checkout::updateById($id, $data);

        $this->assertRequested('PUT', '/checkouts/' . $id, '');
        $this->assertEquals('Test Description', $checkoutObj['description']);
        $this->assertEquals('Test Description', $checkoutObj->description);
    }

    public function testUpdateSaveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('checkout.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $checkoutObj = new Checkout(
            [
                'id' => $id,
                'name' => 'Test Name',
                'description' => 'Test Description'
            ]
        );
        $checkoutObj->save();

        $this->assertRequested('PUT', '/checkouts/' . $id, '');
        $this->assertEquals('Test Description', $checkoutObj['description']);
        $this->assertEquals('Test Description', $checkoutObj->description);
    }
}
