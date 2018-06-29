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

use Apisearch\Query\Filter;
use Apisearch\Result\Aggregation;
use PHPUnit\Framework\TestCase;

/**
 * Class AggregationTest.
 */
class AggregationTest extends TestCase
{
    /**
     * Test construct.
     */
    public function testConstruct()
    {
        $aggregation = new Aggregation(
            'name',
            0,
            10,
            ['1', '2']
        );

        $this->assertEquals('name', $aggregation->getName());
        $this->assertEquals(10, $aggregation->getTotalElements());
        $this->assertEquals([
            '1' => '1',
            '2' => '2',
        ], $aggregation->getActiveElements());
        $this->assertFalse($aggregation->hasLevels());
    }

    /**
     * Test add counter 0 value.
     */
    public function testAddCounter0Value()
    {
        $aggregation = new Aggregation(
            'name',
            0,
            10,
            ['1', '2']
        );
        $aggregation->addCounter('10', 0);
        $this->assertEmpty($aggregation->getCounters());
        $this->assertNull($aggregation->getCounter('10'));
    }

    /**
     * Test add counter invalid name.
     */
    public function testAddCounterInvalidName()
    {
        $aggregation = new Aggregation(
            'name',
            0,
            10,
            ['1', '2']
        );
        $aggregation->addCounter('name##name~~level##10', 10);
        $this->assertEmpty($aggregation->getCounters());
        $this->assertNull($aggregation->getCounter('10'));
    }

    /**
     * Test levels.
     */
    public function testLevels()
    {
        $aggregation = new Aggregation(
            'name',
            Filter::MUST_ALL_WITH_LEVELS,
            10,
            ['1', '2']
        );
        $aggregation->addCounter('id##1~~level##10', 0);
        $this->assertTrue($aggregation->hasLevels());
    }
}
