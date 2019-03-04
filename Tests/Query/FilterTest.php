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
use Apisearch\Query\Filter;
use PHPUnit\Framework\TestCase;

/**
 * Class FilterTest.
 */
class FilterTest extends TestCase
{
    /**
     * Test creation.
     */
    public function testCreate()
    {
        $filter = Filter::create(
            'field',
            [1, 2, 3],
            Filter::MUST_ALL,
            Filter::TYPE_GEO,
            ['a', 'b']
        );

        $this->assertEquals('field', $filter->getField());
        $this->assertEquals([1, 2, 3], $filter->getValues());
        $this->assertEquals(Filter::MUST_ALL, $filter->getApplicationType());
        $this->assertEquals(Filter::TYPE_GEO, $filter->getFilterType());
        $this->assertEquals(['a', 'b'], $filter->getFilterTerms());
    }

    /**
     * Test creation with default values.
     */
    public function testCreateDefaultValues()
    {
        $filter = Filter::create(
            'field',
            [1, 2, 3],
            Filter::MUST_ALL,
            Filter::TYPE_GEO
        );

        $this->assertEquals('field', $filter->getField());
        $this->assertEquals([1, 2, 3], $filter->getValues());
        $this->assertEquals(Filter::MUST_ALL, $filter->getApplicationType());
        $this->assertEquals(Filter::TYPE_GEO, $filter->getFilterType());
        $this->assertEquals([], $filter->getFilterTerms());
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $filterAsArray = [
            'field' => 'field',
            'values' => [1, 2, 3],
            'application_type' => Filter::MUST_ALL,
            'filter_type' => Filter::TYPE_GEO,
            'filter_terms' => ['a', 'b'],
        ];

        $this->assertEquals(
            $filterAsArray,
            Filter::createFromArray($filterAsArray)->toArray()
        );
    }

    /**
     * Test to array.
     */
    public function testToArrayDefaultFields()
    {
        $filterAsArray = [
            'field' => 'uuid.type',
            'values' => [],
            'application_type' => Filter::AT_LEAST_ONE,
            'filter_type' => Filter::TYPE_FIELD,
            'filter_terms' => [],
        ];

        $this->assertEquals(
            [],
            Filter::createFromArray($filterAsArray)->toArray()
        );
    }

    /**
     * Test create from array with defaults.
     */
    public function testCreateFromArrayWithDefaults()
    {
        $filterAsArray = [];
        $aggregation = Filter::createFromArray($filterAsArray);

        $this->assertEquals(
            [],
            $aggregation->toArray()
        );

        $this->assertEquals('uuid.type', $aggregation->getField());
        $this->assertEquals([], $aggregation->getValues());
        $this->assertEquals(Filter::AT_LEAST_ONE, $aggregation->getApplicationType());
        $this->assertEquals(Filter::TYPE_FIELD, $aggregation->getFilterType());
        $this->assertEquals([], $aggregation->getFilterTerms());
    }

    /**
     * Test wrong values format.
     */
    public function testWrongValuesFormat()
    {
        try {
            Filter::createFromArray([
                'values' => 'string',
            ]);
            $this->fail('InvalidFormatException should be thrown');
        } catch (InvalidFormatException $exception) {
            // Silent pass
        }
    }
}
