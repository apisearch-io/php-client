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

namespace Apisearch\Tests\Geo;

use Apisearch\Geo\LocationRange;
use Apisearch\Geo\Polygon;
use Apisearch\Model\Coordinate;
use PHPUnit\Framework\TestCase;

/**
 * Class PolygonTest.
 */
class PolygonTest extends TestCase
{
    /**
     * Test to filter array.
     */
    public function testToFilterArray()
    {
        $polygon = new Polygon([
            new Coordinate(0.0, 1.1),
            new Coordinate(2.0, 1.1),
            new Coordinate(3.0, 1.1),
        ]);

        $this->assertEquals(
            [
                'coordinates' => [
                    [
                        'lat' => 0.0,
                        'lon' => 1.1,
                    ],
                    [
                        'lat' => 2.0,
                        'lon' => 1.1,
                    ],
                    [
                        'lat' => 3.0,
                        'lon' => 1.1,
                    ],
                ],
            ],
            $polygon->toFilterArray()
        );
    }

    /**
     * Test from filter array.
     */
    public function testFromFilterArray()
    {
        $polygonAsArray = [
            'coordinates' => [
                [
                    'lat' => 0.0,
                    'lon' => 1.1,
                ],
                [
                    'lat' => 2.0,
                    'lon' => 1.1,
                ],
                [
                    'lat' => 3.0,
                    'lon' => 1.1,
                ],
            ],
        ];
        $polygon = Polygon::fromFilterArray($polygonAsArray);

        $this->assertEquals(
            $polygonAsArray,
            $polygon->toFilterArray()
        );
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $polygon = new Polygon([
            new Coordinate(0.0, 1.1),
            new Coordinate(2.0, 1.1),
            new Coordinate(3.0, 1.1),
        ]);

        $this->assertEquals(
            [
                'type' => 'Polygon',
                'data' => [
                    'coordinates' => [
                        [
                            'lat' => 0.0,
                            'lon' => 1.1,
                        ],
                        [
                            'lat' => 2.0,
                            'lon' => 1.1,
                        ],
                        [
                            'lat' => 3.0,
                            'lon' => 1.1,
                        ],
                    ],
                ],
            ],
            $polygon->toArray()
        );
    }

    /**
     * Test from array.
     */
    public function testFromArray()
    {
        $polygonAsArray = [
            'type' => 'Polygon',
            'data' => [
                'coordinates' => [
                    [
                        'lat' => 0.0,
                        'lon' => 1.1,
                    ],
                    [
                        'lat' => 2.0,
                        'lon' => 1.1,
                    ],
                    [
                        'lat' => 3.0,
                        'lon' => 1.1,
                    ],
                ],
            ],
        ];
        $polygon = LocationRange::createFromArray($polygonAsArray);

        $this->assertEquals(
            $polygonAsArray,
            $polygon->toArray()
        );
    }
}
