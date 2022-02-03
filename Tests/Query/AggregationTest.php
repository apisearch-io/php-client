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

namespace Apisearch\Tests\Query;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Query\Aggregation;
use Apisearch\Query\Filter;
use PHPUnit\Framework\TestCase;

/**
 * Class AggregationTest.
 */
class AggregationTest extends TestCase
{
    /**
     * Test creation.
     */
    public function testCreate()
    {
        $aggregation = Aggregation::create(
            'name',
            'field',
            Filter::MUST_ALL,
            Filter::TYPE_GEO,
            ['xxx'],
            Aggregation::SORT_BY_COUNT_ASC,
            10
        );

        $this->assertEquals('name', $aggregation->getName());
        $this->assertEquals('field', $aggregation->getField());
        $this->assertEquals(Filter::MUST_ALL, $aggregation->getApplicationType());
        $this->assertEquals(Filter::TYPE_GEO, $aggregation->getFilterType());
        $this->assertEquals(['xxx'], $aggregation->getSubgroup());
        $this->assertEquals(Aggregation::SORT_BY_COUNT_ASC, $aggregation->getSort());
        $this->assertEquals(10, $aggregation->getLimit());
        $this->assertEquals([], $aggregation->getPromoted());
    }

    /**
     * Test creation with bad name.
     */
    public function testCreateBadName()
    {
        $this->expectException(InvalidFormatException::class);
        Aggregation::createFromArray([]);
    }

    /**
     * Test creation with empty name.
     */
    public function testCreateEmptyName()
    {
        $this->expectException(InvalidFormatException::class);
        Aggregation::createFromArray([
            'name' => '',
        ]);
    }

    /**
     * Test creation with default values.
     */
    public function testCreateDefaultValues()
    {
        $aggregation = Aggregation::create(
            'name',
            'field',
            Filter::MUST_ALL,
            Filter::TYPE_GEO
        );

        $this->assertEquals('name', $aggregation->getName());
        $this->assertEquals('field', $aggregation->getField());
        $this->assertEquals(Filter::MUST_ALL, $aggregation->getApplicationType());
        $this->assertEquals(Filter::TYPE_GEO, $aggregation->getFilterType());
        $this->assertEquals([], $aggregation->getSubgroup());
        $this->assertEquals(Aggregation::SORT_BY_COUNT_DESC, $aggregation->getSort());
        $this->assertEquals(Aggregation::NO_LIMIT, $aggregation->getLimit());
        $this->assertEquals([], $aggregation->getPromoted());
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $aggregationAsArray = [
            'name' => 'name',
            'field' => 'uuid.id',
            'application_type' => Filter::MUST_ALL,
            'filter_type' => Filter::TYPE_GEO,
            'subgroup' => ['xxx'],
            'sort' => Aggregation::SORT_BY_COUNT_ASC,
            'limit' => 10,
            'promoted' => ['item1', 'item2'],
        ];

        $this->assertEquals(
            $aggregationAsArray,
            Aggregation::createFromArray($aggregationAsArray)->toArray()
        );
    }

    /**
     * Test to array.
     */
    public function testToArrayDefaultFields()
    {
        $aggregationAsArray = [
            'name' => 'name',
            'field' => 'uuid.type',
            'application_type' => Filter::AT_LEAST_ONE,
            'filter_type' => Filter::TYPE_FIELD,
            'subgroup' => [],
            'sort' => Aggregation::SORT_BY_COUNT_DESC,
            'limit' => 0,
            'promoted' => [],
        ];

        $this->assertEquals(
            [
                'name' => 'name',
            ],
            Aggregation::createFromArray($aggregationAsArray)->toArray()
        );
    }

    /**
     * Test create from array with defaults.
     */
    public function testCreateFromArrayWithDefaults()
    {
        $aggregationAsArray = [
            'name' => 'name',
        ];
        $aggregation = Aggregation::createFromArray($aggregationAsArray);

        $this->assertEquals(
            [
                'name' => 'name',
            ],
            $aggregation->toArray()
        );

        $this->assertEquals('name', $aggregation->getName());
        $this->assertEquals('uuid.type', $aggregation->getField());
        $this->assertEquals(Filter::AT_LEAST_ONE, $aggregation->getApplicationType());
        $this->assertEquals(Filter::TYPE_FIELD, $aggregation->getFilterType());
        $this->assertEquals([], $aggregation->getSubgroup());
        $this->assertEquals(Aggregation::SORT_BY_COUNT_DESC, $aggregation->getSort());
        $this->assertEquals(Aggregation::NO_LIMIT, $aggregation->getLimit());
        $this->assertEquals([], $aggregation->getPromoted());
    }
}
