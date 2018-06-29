<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Tests\Result;

use Apisearch\Result\Counter;
use PHPUnit\Framework\TestCase;

/**
 * Class CounterTest.
 */
class CounterTest extends TestCase
{
    /**
     * Test simple creation.
     */
    public function testCreateUsedByActiveElements()
    {
        $counter = Counter::createByActiveElements(
            'id##1~~name##category1',
            10,
            ['1', '2']
        );

        $this->assertEquals('1', $counter->getId());
        $this->assertEquals('category1', $counter->getName());
        $this->assertNull($counter->getSlug());
        $this->assertEquals(10, $counter->getN());
        $this->assertTrue($counter->isUsed());
        $this->assertEquals([
            'id' => '1',
            'name' => 'category1',
        ], $counter->getValues());
        $this->assertEquals(0, $counter->getLevel());
    }

    /**
     * Test simple creation.
     */
    public function testCreateNotUsedByActiveElements()
    {
        $counter = Counter::createByActiveElements(
            'id##1~~name##category1',
            10,
            ['3', '2']
        );

        $this->assertFalse($counter->isUsed());
    }

    /**
     * Test invalid name.
     */
    public function testInvalidName()
    {
        $counter = Counter::createByActiveElements(
            'name##category1~~hola##hola',
            10,
            ['3', '2']
        );

        $this->assertNull($counter);
    }

    /**
     * Test to array all values.
     */
    public function testToArrayDefaultValues()
    {
        $this->assertEquals(
            [
                'values' => [
                    'id' => '1',
                    'name' => '1',
                ],
                'n' => 10,
            ],
            Counter::createByActiveElements('1', 10, [])->toArray()
        );
    }

    /**
     * Test to array all values.
     */
    public function testToArray()
    {
        $this->assertEquals(
            [
                'values' => [
                    'id' => '1',
                    'name' => '1',
                ],
                'used' => true,
                'n' => 10,
            ],
            Counter::createByActiveElements('1', 10, ['1'])->toArray()
        );
    }

    /**
     * Test create from array with default values.
     */
    public function testCreateFromArrayDefaultValues()
    {
        $counter = Counter::createFromArray([
            'values' => [
                'id' => '1',
                'name' => '1',
            ],
            'n' => 10,
        ]);

        $this->assertEquals([
            'id' => '1',
            'name' => '1',
        ], $counter->getValues());
        $this->assertFalse($counter->isUsed());
        $this->assertEquals(10, $counter->getN());
    }

    /**
     * Test create from array with all values.
     */
    public function testCreateFromArray()
    {
        $counter = Counter::createFromArray([
            'values' => [
                'id' => '1',
                'name' => '1',
            ],
            'used' => true,
            'n' => 10,
        ]);

        $this->assertEquals([
            'id' => '1',
            'name' => '1',
        ], $counter->getValues());
        $this->assertTrue($counter->isUsed());
        $this->assertEquals(10, $counter->getN());
    }
}
