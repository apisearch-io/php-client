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

use Apisearch\Result\Aggregation;
use Apisearch\Result\Aggregations;
use PHPUnit\Framework\TestCase;

/**
 * Class AggregationsTest.
 */
class AggregationsTest extends TestCase
{
    /**
     * Test construct.
     */
    public function testConstruct()
    {
        $aggregations = new Aggregations(100);
        $aggregations->addAggregation('name', new Aggregation('name', 0, 10, ['1']));
        $aggregations->addAggregation('name2', new Aggregation('name2', 0, 10, ['2']));
        $this->assertCount(2, $aggregations);
        $this->assertCount(2, $aggregations->getAggregations());
        $this->assertEquals(100, $aggregations->getTotalElements());
        $this->assertInstanceOf(Aggregation::class, $aggregations->getAggregation('name'));
        $this->assertEquals('name', $aggregations->getAggregation('name')->getName());
        $this->assertInstanceOf(Aggregation::class, $aggregations->getAggregation('name2'));
        $this->assertEquals('name2', $aggregations->getAggregation('name2')->getName());
        $this->assertTrue($aggregations->hasNotEmptyAggregation('name'));
        $this->assertTrue($aggregations->hasNotEmptyAggregation('name2'));
    }

    /**
     * Test http layer default values.
     */
    public function testHttpLayerDefaultValues()
    {
        $this->assertEquals(
            [],
            Aggregations::createFromArray([])->toArray()
        );
    }

    /**
     * Test http layer default values.
     */
    public function testHttpLayer()
    {
        $aggregationsAsArray = [
            'total_elements' => 100,
            'aggregations' => [
                'name' => [
                    'name' => 'name',
                ],
                'name2' => [
                    'name' => 'name2',
                ],
            ],
        ];

        $this->assertEquals(
            $aggregationsAsArray,
            Aggregations::createFromArray($aggregationsAsArray)->toArray()
        );
    }
}
