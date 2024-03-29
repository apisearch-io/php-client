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

use Apisearch\Model\Coordinate;
use Apisearch\Query\Filter;
use Apisearch\Query\SortBy;
use Apisearch\Tests\HttpHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class SortByTest.
 */
class SortByTest extends TestCase
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

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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
                    'field' => 'indexed_metadata.category',
                    'order' => SortBy::ASC,
                ],
                [
                    'type' => SortBy::TYPE_FIELD,
                    'field' => 'indexed_metadata.brand',
                    'order' => SortBy::DESC,
                ],
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
        );
    }

    /**
     * Test sort by field value.
     */
    public function testSortByFieldValue()
    {
        $sortBy = SortBy::create();
        $sortBy->byFieldValue('category', SortBy::ASC);
        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_FIELD,
                    'field' => 'indexed_metadata.category',
                    'order' => SortBy::ASC,
                ],
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
        );
    }

    /**
     * Test sort by field value.
     */
    public function testSortByFieldValueBasicFields()
    {
        $sortBy = SortBy::create();
        $sortBy->byFieldValue('id', SortBy::ASC);
        $sortBy->byFieldValue('type', SortBy::ASC);
        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_FIELD,
                    'field' => 'uuid.id',
                    'order' => SortBy::ASC,
                ],
                [
                    'type' => SortBy::TYPE_FIELD,
                    'field' => 'uuid.type',
                    'order' => SortBy::ASC,
                ],
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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
                    'field' => 'indexed_metadata.category',
                    'order' => SortBy::ASC,
                    'mode' => SortBy::MODE_AVG,
                    'filter' => Filter::create('uuid.id', [1, 2], Filter::AT_LEAST_ONE, Filter::TYPE_FIELD),
                ],
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
        );
    }

    /**
     * Test http transport.
     */
    public function testCreateFromArray()
    {
        $sortBy = [
            [
                'type' => SortBy::TYPE_FIELD,
                'field' => 'indexed_metadata.category',
                'order' => SortBy::ASC,
            ],
            [
                'type' => SortBy::TYPE_FIELD,
                'field' => 'indexed_metadata.brand',
                'order' => SortBy::ASC,
            ],
            [
                'type' => SortBy::TYPE_NESTED,
                'field' => 'indexed_metadata.brand',
                'order' => SortBy::ASC,
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

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport(SortBy::createFromArray($sortBy))->toArray()
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

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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
            ->byValue(SortBy::AL_TUN_TUN)
            ->setCoordinate($coordinate);

        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_DISTANCE,
                    'coordinate' => $coordinate,
                    'unit' => 'km',
                ],
                SortBy::AL_TUN_TUN,
            ],
            $sortBy->all()
        );

        $this->assertEquals(
            [
                [
                    'type' => SortBy::TYPE_DISTANCE,
                    'coordinate' => $coordinate->toArray(),
                    'unit' => 'km',
                ],
                SortBy::AL_TUN_TUN,
            ],
            $sortBy->toArray()
        );

        $this->assertEquals(
            $sortBy,
            SortBy::createFromArray([
                [
                    'type' => SortBy::TYPE_DISTANCE,
                    'coordinate' => $coordinate->toArray(),
                    'unit' => 'km',
                ],
                SortBy::AL_TUN_TUN,
            ])
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport(SortBy::createFromArray([
                [
                    'type' => SortBy::TYPE_DISTANCE,
                    'coordinate' => $coordinate->toArray(),
                    'unit' => 'km',
                ],
                SortBy::AL_TUN_TUN,
            ]))
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
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

    /**
     * Test multiples sorts.
     */
    public function testMultiplesSorts()
    {
        $sortBy = SortBy::create()
            ->byValue(SortBy::SCORE)
            ->byValue(SortBy::LOCATION_KM_ASC)
            ->byValue(SortBy::AL_TUN_TUN)
            ->byNestedFieldAndFilter(
                'category',
                SortBy::ASC,
                Filter::create('id', [1, 2], Filter::AT_LEAST_ONE, Filter::TYPE_FIELD)
            );

        $this->assertCount(
            4,
            $sortBy->all()
        );

        $this->assertCount(
            4,
            SortBy::createFromArray($sortBy->toArray())->all()
        );

        $this->assertEquals(
            $sortBy,
            HttpHelper::emulateHttpTransport($sortBy)
        );
    }

    public function testByFunction()
    {
        $sortBy = SortBy::create()->byFunction('function 1', 'asc', ['val1', 'val2']);
        $sortByAsArray = [
            [
                'type' => 'function',
                'function' => 'function 1',
                'order' => 'asc',
                'params' => ['val1', 'val2'],
            ],
        ];

        $this->assertEquals($sortByAsArray, $sortBy->toArray());
        $this->assertEquals($sortByAsArray, SortBy::createFromArray($sortByAsArray)->toArray());
    }
}
