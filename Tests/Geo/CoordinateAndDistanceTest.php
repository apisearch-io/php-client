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

use Apisearch\Geo\CoordinateAndDistance;
use Apisearch\Geo\LocationRange;
use Apisearch\Model\Coordinate;
use PHPUnit\Framework\TestCase;

/**
 * Class CoordinateAndDistanceTest.
 */
class CoordinateAndDistanceTest extends TestCase
{
    /**
     * Test to filter array.
     */
    public function testToFilterArray()
    {
        $coordinateAndDistance = new CoordinateAndDistance(
            new Coordinate(0.0, 1.1),
            '10km'
        );

        $this->assertEquals(
            [
                'coordinate' => [
                    'lat' => 0.0,
                    'lon' => 1.1,
                ],
                'distance' => '10km',
            ],
            $coordinateAndDistance->toFilterArray()
        );
    }

    /**
     * Test from filter array.
     */
    public function testFromFilterArray()
    {
        $coordinateAndDistanceAsArray = [
            'coordinate' => [
                'lat' => 0.0,
                'lon' => 1.1,
            ],
            'distance' => '10km',
        ];
        $coordinateAndDistance = CoordinateAndDistance::fromFilterArray($coordinateAndDistanceAsArray);

        $this->assertEquals(
            $coordinateAndDistanceAsArray,
            $coordinateAndDistance->toFilterArray()
        );
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $coordinateAndDistance = new CoordinateAndDistance(
            new Coordinate(0.0, 1.1),
            '10km'
        );

        $this->assertEquals(
            [
                'type' => 'CoordinateAndDistance',
                'data' => [
                    'coordinate' => [
                        'lat' => 0.0,
                        'lon' => 1.1,
                    ],
                    'distance' => '10km',
                ],
            ],
            $coordinateAndDistance->toArray()
        );
    }

    /**
     * Test from array.
     */
    public function testFromArray()
    {
        $coordinateAndDistanceAsArray = [
            'type' => 'CoordinateAndDistance',
                'data' => [
                    'coordinate' => [
                        'lat' => 0.0,
                        'lon' => 1.1,
                    ],
                    'distance' => '10km',
                ],
        ];
        $coordinateAndDistance = LocationRange::createFromArray($coordinateAndDistanceAsArray);

        $this->assertEquals(
            $coordinateAndDistanceAsArray,
            $coordinateAndDistance->toArray()
        );
    }
}
