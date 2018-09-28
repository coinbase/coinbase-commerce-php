<?php
namespace CoinbaseCommerce\Tests\Resources;

use CoinbaseCommerce\ApiResourceList;
use CoinbaseCommerce\Resources\Charge;
use CoinbaseCommerce\Resources\Event;
use CoinbaseCommerce\Tests\BaseTest;

class EventTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();
        Event::setClient($this->apiClient);
    }

    public function testRefreshMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('event.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $eventObj = new Event();
        $eventObj->id = $id;
        $eventObj->refresh();

        $this->assertRequested('GET', '/events/' . $id, '');
        $this->assertEquals($eventObj->type, 'charge:failed');
    }

    public function testRetrieveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('event.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $eventObj = Event::retrieve($id);

        $this->assertRequested('GET', '/events/' . $id, '');
        $this->assertEquals($eventObj->type, 'charge:failed');
        $this->assertInstanceOf(Charge::getClassName(), $eventObj->data);
    }

    public function testListMethod()
    {
        $eventResponse = $this->parseJsonFile('eventList.json');
        $this->appendRequest(200, $eventResponse);
        $this->logger->expects($this->once())
            ->method('warning')
            ->with(implode(',', $eventResponse['warnings']));
        $eventList = Event::getList(['limit' => 2, 'order' => 'asc']);

        $this->assertRequested('GET', '/events', 'limit=2&order=asc');
        $this->assertInstanceOf(ApiResourceList::getClassName(), $eventList);
        $this->assertEquals('charge:failed', $eventList[0]['type']);
        $this->assertEquals('Satoshi Nakamoto', $eventList[0]['data']['metadata']['customer_name']);
    }
}
