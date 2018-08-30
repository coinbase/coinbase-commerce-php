<?php
namespace CoinbaseCommerce\Tests;

use CoinbaseCommerce\Resources\ApiResource;
use PHPUnit\Framework\TestCase;

class ApiResourceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->apiResourceStub = $this->getMockForAbstractClass(ApiResource::getClassName());
    }

    public function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    public function testRefreshFrom()
    {
        $data = [
            'name' => 'Test Name',
            'meta' => [
                'option1' => 'value1'
            ]
        ];

        $this->callMethod(
            $this->apiResourceStub,
            'refreshFrom',
            [
                $data
            ]
        );

        $allAttributes = $this->callMethod($this->apiResourceStub, 'getAttributes', []);

        $this->assertEquals('Test Name', $this->apiResourceStub->name);
        $this->assertEquals('Test Name', $this->apiResourceStub['name']);
        $this->assertEquals('Test Name', $this->apiResourceStub->getAttribute('name'));
        $this->assertEquals('value1', $this->apiResourceStub->meta['option1']);
        $this->assertEquals('value1', $this->apiResourceStub['meta']['option1']);
        $this->assertEquals($data, $allAttributes);
    }

    public function testUpdateAttributes()
    {
        $this->callMethod(
            $this->apiResourceStub,
            'refreshFrom',
            [
                [
                    'name' => 'Test Name',
                    'meta' => [
                        'option1' => 'value1'
                    ]
                ]
            ]
        );

        $this->assertEquals('Test Name', $this->apiResourceStub->name);
        $this->assertEquals('value1', $this->apiResourceStub->meta['option1']);
        $this->apiResourceStub->name = 'New Name';
        $this->apiResourceStub->meta['option1'] = 'value2';
        $this->assertEquals('New Name', $this->apiResourceStub->name);
        $this->assertEquals('value2', $this->apiResourceStub->meta['option1']);
    }

    public function testDeleteAttribute()
    {
        $this->callMethod(
            $this->apiResourceStub,
            'refreshFrom',
            [
                [
                    'name' => 'Test Name',
                    'meta' => [
                        'option1' => 'value1'
                    ]
                ]
            ]
        );

        unset($this->apiResourceStub['name']);
        $this->assertArrayNotHasKey('name', $this->apiResourceStub);
    }

    public function testDirtyAttributes()
    {
        $this->callMethod(
            $this->apiResourceStub,
            'refreshFrom',
            [
                [
                    'name' => 'Test Name',
                    'description' => 'Test Description',
                    'meta' => [
                        'option1' => 'value1',
                        'option2' => 'value2'
                    ]
                ]
            ]
        );

        $this->apiResourceStub->name = 'New Name';
        $this->apiResourceStub->meta['option2'] = 'new value';

        $dirtyAttributes = $this->apiResourceStub->getDirtyAttributes();

        $this->assertArrayHasKey('name', $dirtyAttributes);
        $this->assertArrayHasKey('meta', $dirtyAttributes);
        $this->assertArrayNotHasKey('description', $dirtyAttributes);
    }

    public function testClearAttributes()
    {
        $this->callMethod(
            $this->apiResourceStub,
            'refreshFrom',
            [
                [
                    'name' => 'Test Name',
                    'description' => 'Test Description',
                    'meta' => [
                        'option1' => 'value1',
                        'option2' => 'value2'
                    ]
                ]
            ]
        );
        $this->callMethod($this->apiResourceStub, 'clearAttributes', []);

        $this->assertEquals([], $this->apiResourceStub->getAttributes());
        $this->assertEquals([], $this->apiResourceStub->getDirtyAttributes());
    }
}
