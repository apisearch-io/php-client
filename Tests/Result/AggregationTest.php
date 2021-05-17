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
use Apisearch\Result\Counter;
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
            ['1', '2'],
            [
                'a' => 1,
                'b' => 2,
            ]
        );

        $this->assertEquals('name', $aggregation->getName());
        $this->assertEquals(10, $aggregation->getTotalElements());
        $this->assertEquals([
            '1' => '1',
            '2' => '2',
        ], $aggregation->getActiveElements());
        $this->assertFalse($aggregation->hasLevels());
        $this->assertFalse($aggregation->isFilter());
        $this->assertFalse($aggregation->isEmpty());
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
        ], $aggregation->getMetadata());
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

    /**
     * Test levels.
     */
    public function testEmpty()
    {
        $aggregation = new Aggregation(
            'name',
            Filter::MUST_ALL_WITH_LEVELS,
            10,
            []
        );
        $this->assertTrue($aggregation->isEmpty());

        $aggregation = new Aggregation(
            'name',
            0,
            10,
            []
        );
        $this->assertTrue($aggregation->isEmpty());
        $this->assertEquals([], $aggregation->getMetadata());
    }

    /**
     * Test get active elements.
     */
    public function testGetActiveElements()
    {
        $aggregation = new Aggregation(
            'name',
            Filter::AT_LEAST_ONE,
            10,
            ['1', '2']
        );
        $aggregation->addCounter('id##1~~name##product', 10);
        $aggregation->addCounter('id##2~~name##product2', 5);
        $this->assertCount(2, $aggregation->getActiveElements());
        $this->assertCount(2, $aggregation->getCounters());
        $this->assertCount(2, $aggregation->getAllElements());

        $this->assertEquals('1', $aggregation->getAllElements()['1']->getId());
        $this->assertEquals('2', $aggregation->getAllElements()['2']->getId());
    }

    /**
     * Test clean counters by level.
     *
     * @group mmm
     */
    public function testCleanCountersByLevel()
    {
        $aggregation = new Aggregation(
            'name',
            Filter::MUST_ALL_WITH_LEVELS,
            10,
            ['1', '2']
        );
        $aggregation->addCounter('id##1~~level##1', 10);
        $aggregation->addCounter('id##2~~level##2', 10);
        $aggregation->addCounter('id##3~~level##2', 10);
        $aggregation->cleanCountersByLevel();
        $this->assertCount(1, $aggregation->getActiveElements());
        $aggregation->cleanCountersByLevel();
        $this->assertCount(1, $aggregation->getActiveElements());
        $this->assertEquals('2', $aggregation->getActiveElements()[0]->getId());
        $aggregation->addCounter('id##4~~level##1', 4);
        $aggregation->cleanCountersByLevel();
        $this->assertCount(1, $aggregation->getActiveElements());
        $this->assertEquals('2', $aggregation->getActiveElements()[0]->getId());
    }

    /**
     * Test to array all values.
     */
    public function testToArrayDefaultValues()
    {
        $aggregation = new Aggregation('name', Filter::AT_LEAST_ONE, 0, []);
        $this->assertEquals(
            [
                'name' => 'name',
            ],
            $aggregation->toArray()
        );
    }

    /**
     * Test to array all values.
     */
    public function testToArrayAllValues()
    {
        $aggregation = new Aggregation('name', Filter::MUST_ALL, 100, ['1'], ['a' => 1, 'b' => 2]);
        $aggregation->addCounter('1', 10);
        $aggregation->addCounter('2', 10);
        $this->assertEquals(
            [
                'name' => 'name',
                'counters' => [
                    Counter::createByActiveElements('1', 10, ['1'])->toArray(),
                    Counter::createByActiveElements('2', 10, ['1'])->toArray(),
                ],
                'application_type' => Filter::MUST_ALL,
                'active_elements' => [
                    '1',
                ],
                'total_elements' => 100,
                'metadata' => ['a' => 1, 'b' => 2],
            ],
            $aggregation->toArray()
        );
    }

    /**
     * Test create from array default values.
     */
    public function testCreateFromArrayDefaultValues()
    {
        $aggregation = Aggregation::createFromArray(['name' => 'agg1']);
        $this->assertEquals('agg1', $aggregation->getName());
        $this->assertCount(0, $aggregation->getCounters());
        $this->assertEquals(0, $aggregation->getTotalElements());
        $this->assertCount(0, $aggregation->getActiveElements());
    }

    /**
     * Test create from array all values.
     */
    public function testCreateFromArrayAllValues()
    {
        $aggregation = Aggregation::createFromArray([
            'name' => 'agg1',
            'counters' => [
                Counter::createByActiveElements('1', 10, ['1'])->toArray(),
                Counter::createByActiveElements('2', 10, ['1'])->toArray(),
            ],
            'application_type' => Filter::MUST_ALL,
            'active_elements' => [
                '1',
            ],
            'total_elements' => 100,
            'metadata' => ['a' => 1, 'b' => 2],
        ]);
        $this->assertEquals('agg1', $aggregation->getName());
        $this->assertCount(2, $aggregation->getCounters());
        $this->assertEquals(100, $aggregation->getTotalElements());
        $this->assertCount(1, $aggregation->getActiveElements());
    }
}
