<?php
namespace CoinbaseCommerce\Tests\Resources;

use CoinbaseCommerce\ApiResourceList;
use CoinbaseCommerce\Resources\Charge;
use CoinbaseCommerce\Tests\BaseTest;

class ChargeTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        Charge::setClient($this->apiClient);
    }

    public function testInsertMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $data = [
            'name' => 'My test name',
            'description' => 'Mastering the Transition to the Information Age'
        ];
        $chargeObj = new Charge($data);
        $chargeObj->insert();

        $this->assertRequested('POST', '/charges', '');
        $this->assertEquals('7C7V5ECK', $chargeObj->code);
    }

    public function testSaveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $chargeObj = new Charge(
            [
                'name' => 'My test name',
                'description' => 'Mastering the Transition to the Information Age'
            ]
        );
        $chargeObj->save();

        $this->assertRequested('POST', '/charges', '');
        $this->assertInstanceOf(Charge::getClassName(), $chargeObj);
        $this->assertEquals('7C7V5ECK', $chargeObj->code);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Update is not allowed
     */
    public function testSaveMethodWithId()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $chargeObj = new Charge(
            [
                'id' => '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe',
                'name' => 'My test name',
                'description' => 'Mastering the Transition to the Information Age'
            ]
        );
        $chargeObj->save();
    }

    public function testCreateMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $chargeObj = Charge::create(
            [
                'name' => 'My test name',
                'description' => 'Mastering the Transition to the Information Age'
            ]
        );

        $this->assertRequested('POST', '/charges', '');
        $this->assertInstanceOf(Charge::getClassName(), $chargeObj);
        $this->assertEquals('7C7V5ECK', $chargeObj->code);
    }

    public function testRefreshMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $chargeObj = new Charge();
        $chargeObj->id = $id;
        $chargeObj->refresh();

        $this->assertRequested('GET', '/charges/' . $id, '');
        $this->assertInstanceOf(Charge::getClassName(), $chargeObj);
        $this->assertEquals('7C7V5ECK', $chargeObj->code);
    }

    public function testRetrieveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $chargeObj = Charge::retrieve($id);

        $this->assertRequested('GET', '/charges/' . $id, '');
        $this->assertInstanceOf(Charge::getClassName(), $chargeObj);
        $this->assertEquals('7C7V5ECK', $chargeObj->code);
    }

    public function testListMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('chargeList.json'));
        $chargeList = Charge::getList(['limit' => 2]);

        $this->assertRequested('GET', '/charges', 'limit=2');
        $this->assertInstanceOf(ApiResourceList::getClassName(), $chargeList);
    }

    public function testAllMethod()
    {
        $firstPageChargeList = $this->parseJsonFile('firstPageChargeList.json');
        $startingAfter = $firstPageChargeList['pagination']['cursor_range'][1];
        $this->appendRequest(200, $firstPageChargeList);
        $this->appendRequest(200, $this->parseJsonFile('secondPageChargeList.json'));
        $list = Charge::getAll(['order' => 'desc']);

        $this->assertRequested('GET', '/charges', 'order=desc');
        $this->assertRequested('GET', '/charges', 'order=desc&starting_after=' . $startingAfter);
        $this->assertCount(3, $list);
        $this->assertInstanceOf(Charge::getClassName(), $list[0]);
    }

    public function testResolveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $chargeObj = Charge::retrieve($id);
        $this->assertRequested('GET', '/charges/' . $id, '');
        $id = $chargeObj->id;
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $chargeObj->resolve();

        $this->assertRequested('POST', "/charges/$id/resolve", '');
        $this->assertEquals($id, $chargeObj->id);
    }

    public function testCancelMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $chargeObj = Charge::retrieve($id);
        $this->assertRequested('GET', '/charges/' . $id, '');
        $id = $chargeObj->id;
        $this->appendRequest(200, $this->parseJsonFile('charge.json'));
        $chargeObj->cancel();

        $this->assertRequested('POST', "/charges/$id/cancel", '');
        $this->assertEquals($id, $chargeObj->id);
    }
}
