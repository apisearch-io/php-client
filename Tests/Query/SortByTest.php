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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Tests\Query;

use Apisearch\Model\Coordinate;
use Apisearch\Query\Filter;
use Apisearch\Query\SortBy;
use PHPUnit_Framework_TestCase;

/**
 * Class SortByTest.
 */
class SortByTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test empty object.
     */
    public function testEmpty()
    {
        $sortBy = SortBy::createFromArray([]);
        $this->assertEquals(
            [],
            $sortBy->toArray()
        );

        $sortBy = SortBy::create();
        $this->assertEquals(
            [],
            $sortBy->toArray()
        );
    }

    /**
     * Test simple sort by.
     */
    public function testSimpleSortBy()
    {
        $sortByArray = SortBy::AL_TUN_TUN;
        $sortBy = SortBy::create()->byValue(SortBy::AL_TUN_TUN);
        $this->assertEquals(
            [$sortByArray],
            $sortBy->all()
        );
    }

    /**
     * Test create from fields and values.
     */
    public function testFromFieldsAndValues()
    {
        $sortBy = SortBy::byFieldsValues([
            'category' => 'asc',
            'brand' => 'desc',
        ]);
        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_FIELD,
                    'indexed_metadata.category' => [
                        'order' => SortBy::ASC,
                    ],
                ],
                [
                    'type' => SortBy::TYPE_FIELD,
                    'indexed_metadata.brand' => [
                        'order' => SortBy::DESC,
                    ],
                ],
            ],
            $sortBy->all()
        );
    }

    /**
     * Test empty sort by.
     */
    public function testEmptySortBy()
    {
        $sortBy = SortBy::create();
        $this->assertEquals(
            [SortBy::SCORE],
            $sortBy->all()
        );
    }

    /**
     * Test empty sort by.
     */
    public function testSortByField()
    {
        $sortBy = SortBy::create();
        $sortBy->byFieldValue('category', SortBy::ASC);
        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_FIELD,
                    'indexed_metadata.category' => [
                        'order' => SortBy::ASC,
                    ],
                ],
            ],
            $sortBy->all()
        );
    }

    /**
     * Test sortby nested field and filter.
     */
    public function testNestedFieldAndFilter()
    {
        $sortBy = SortBy::create();
        $sortBy->byNestedFieldAndFilter(
            'category',
            SortBy::ASC,
            Filter::create('id', [1, 2], Filter::AT_LEAST_ONE, Filter::TYPE_FIELD)
        );

        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_NESTED,
                    'indexed_metadata.category' => [
                        'order' => SortBy::ASC,
                    ],
                    'mode' => SortBy::MODE_AVG,
                    'filter' => Filter::create('uuid.id', [1, 2], Filter::AT_LEAST_ONE, Filter::TYPE_FIELD),
                ],
            ],
            $sortBy->all()
        );
    }

    /**
     * Test http transport.
     */
    public function testCreateFromArray()
    {
        $sortBy = [
            [
                'indexed_metadata.category' => [
                    'order' => SortBy::ASC,
                ],
            ],
            [
                'indexed_metadata.brand' => [
                    'order' => SortBy::ASC,
                ],
            ],
            [
                'type' => SortBy::TYPE_NESTED,
                'indexed_metadata.brand' => [
                    'order' => SortBy::ASC,
                ],
                'filter' => Filter::create(
                    'a',
                    ['n'],
                    Filter::MUST_ALL,
                    Filter::TYPE_FIELD
                )->toArray(),
            ],
        ];

        $this->assertEquals(
            $sortBy,
            SortBy::createFromArray($sortBy)->toArray()
        );
    }

    /**
     * Test http transport.
     */
    public function testToArray()
    {
        $sortBy = SortBy::create();
        $filter = Filter::create('id', [1, 2], Filter::AT_LEAST_ONE, Filter::TYPE_FIELD);
        $sortBy->byFieldValue('category', SortBy::ASC);
        $sortBy->byFieldValue('brand', SortBy::ASC);
        $sortBy
            ->byValue(SortBy::ID_DESC)
            ->byValue(SortBy::AL_TUN_TUN);
        $sortBy->byNestedFieldAndFilter(
            'category',
            SortBy::ASC,
            $filter
        );

        $this->assertEquals(
            $sortBy,
            SortBy::createFromArray($sortBy->toArray())
        );
    }

    /**
     * Test is sorted by geo distance.
     */
    public function testIsSortedByGeoDistance()
    {
        $this->assertFalse(SortBy::create()->isSortedByGeoDistance());
        $this->assertFalse(SortBy::createFromArray([['category' => 'asc']])->isSortedByGeoDistance());
        $this->assertFalse(SortBy::create()->byValue(SortBy::AL_TUN_TUN)->isSortedByGeoDistance());
        $this->assertTrue(SortBy::create()->byValue(SortBy::LOCATION_KM_ASC)->isSortedByGeoDistance());
        $this->assertTrue(SortBy::create()->byValue(SortBy::LOCATION_MI_ASC)->isSortedByGeoDistance());
    }

    /**
     * Test coordinate injection.
     */
    public function testCoordinateInjection()
    {
        $coordinate = new Coordinate(10.0, 20.0);
        $sortBy = SortBy::create()
            ->byValue(SortBy::LOCATION_KM_ASC)
            ->setCoordinate($coordinate);

        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_FIELD,
                    '_geo_distance' => [
                        'coordinate' => $coordinate,
                        'order' => SortBy::ASC,
                        'unit' => 'km',
                    ],
                ],
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            [
                [
                    '_geo_distance' => [
                        'coordinate' => $coordinate->toArray(),
                        'order' => SortBy::ASC,
                        'unit' => 'km',
                    ],
                ],
            ],
            $sortBy->toArray()
        );

        $this->assertEquals(
            $sortBy,
            SortBy::createFromArray([
                [
                    '_geo_distance' => [
                        'coordinate' => $coordinate->toArray(),
                        'order' => SortBy::ASC,
                        'unit' => 'km',
                    ],
                ],
            ])
        );
    }

    /**
     * Test has random sort.
     */
    public function testHasRandomSort()
    {
        $this->assertFalse(SortBy::create()->hasRandomSort());
        $this->assertFalse(SortBy::create()->byFieldValue('random', 'asc')->hasRandomSort());
        $this->assertTrue(SortBy::create()->byValue(SortBy::RANDOM)->hasRandomSort());
        $this->assertTrue(SortBy::create()->byValue(SortBy::AL_TUN_TUN)->hasRandomSort());
    }
}
