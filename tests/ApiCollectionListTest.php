<?php
namespace CoinbaseCommerce\Tests;

use CoinbaseCommerce\ApiResourceList;
use CoinbaseCommerce\Resources\Charge;

class ApiCollectionListTest extends BaseTest
{
    public function testInitCollection()
    {
        Charge::setClient($this->apiClient);
        ApiResourceList::setClient($this->apiClient);

        $firstPageChargeList = $this->parseJsonFile('firstPageChargeList.json');
        $secondPageChargeList = $this->parseJsonFile('secondPageChargeList.json');

        $this->appendRequest(200, $firstPageChargeList);
        $this->appendRequest(200, $secondPageChargeList);

        $list = Charge::getList(['limit' => 2]);

        $this->assertInstanceOf(Charge::getClassName(), $list[0]);
        $this->assertCount(2, $list);
        $this->assertEquals(3, $list->countAll());
        $this->assertEquals($firstPageChargeList['pagination'], $list->getPagination());
        $this->assertTrue($list->hasNext());
        $this->assertTrue($list->hasPrev());

        $list->loadNext();

        $this->assertInstanceOf(Charge::getClassName(), $list[0]);
        $this->assertEquals($firstPageChargeList['data'][0]['name'], $list[0]['name']);
        $this->assertEquals(3, $list->countAll());
        $this->assertCount(1, $list);
        $this->assertEquals($secondPageChargeList['pagination'], $list->getPagination());

        $this->assertRequested('GET', '/charges', 'limit=2');
        $this->assertRequested('GET', '/charges', 'limit=2&starting_after=' . $firstPageChargeList['pagination']['cursor_range'][1]);
    }
}
