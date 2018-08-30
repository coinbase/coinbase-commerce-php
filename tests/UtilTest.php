<?php
namespace CoinbaseCommerce\Tests;

use PHPUnit\Framework\TestCase;
use CoinbaseCommerce\Util;

class UtilTest extends TestCase
{
    public function testHashEqualSuccess()
    {
        $this->assertTrue(Util::hashEqual('8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590', '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590'));
        $this->assertFalse(Util::hashEqual('8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa09259r', '8be7742c7d372f08a6a3224edadf18a22b65fa9e28f3f2de97376cdaa092590'));
        $this->assertFalse(Util::hashEqual('sdfdsfsdf', 'sd'));
    }

    public function testEqual()
    {
        $this->assertTrue(
            Util::equal(
                [
                   'prop1' => [
                       'value1'
                   ],
                   'prop2' => [
                       'propinheritlevel1' => [
                           'propinheritlevel2' => 'valueinheritlevel2'
                       ]
                   ]
                ],
                [
                    'prop1' => [
                        'value1'
                    ],
                    'prop2' => [
                        'propinheritlevel1' => [
                            'propinheritlevel2' => 'valueinheritlevel2'
                        ]
                    ]
                ]
            )
        );
        $this->assertFalse(
            Util::equal(
                [
                    'prop1' => [
                        'value1'
                    ],
                    'prop2' => [
                        'propinheritlevel1' => [
                            'propinheritlevel2' => 'valueinheritlevel2'
                        ]
                    ]
                ],
                [
                    'prop1' => [
                        'value1'
                    ],
                    'prop2' => [
                        'propinheritlevel1' => [
                            'propinheritlevel2' => 'anothervalueinheritlevel2'
                        ]
                    ]
                ]
            )
        );
        $this->assertFalse(
            Util::equal(
                [
                    'prop1' => [
                        'value1'
                    ],
                    'prop2' => [
                        'propinheritlevel1' => [
                            'propinheritlevel2' => 'valueinheritlevel2'
                        ]
                    ]
                ],
                [
                    'prop1' => [
                        'value1'
                    ],
                    'prop2' => [
                        'propinheritlevel1' => [
                            'anotherpropinheritlevel2' => 'valueinheritlevel2'
                        ]
                    ]
                ]
            )
        );
        $this->assertFalse(Util::equal([], false));
        $this->assertTrue(Util::equal([], []));
        $this->assertTrue(Util::equal('some test string', 'some test string'));
    }

    public function testGetResourcePath()
    {
        $this->assertEquals('part1/part2', Util::joinPath('part1/', 'part2'));
        $this->assertEquals('part1/part2', Util::joinPath('/part1/', '/part2/'));
        $this->assertEquals('http://test.com/part1?key=value', Util::joinPath('http://test.com', 'part1?key=value'));
    }
}
