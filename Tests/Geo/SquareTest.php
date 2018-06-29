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
use Apisearch\Geo\Square;
use Apisearch\Model\Coordinate;
use PHPUnit\Framework\TestCase;

/**
 * Class SquareTest.
 */
class SquareTest extends TestCase
{
    /**
     * Test to filter array.
     */
    public function testToFilterArray()
    {
        $square = new Square(
            new Coordinate(0.0, 1.1),
            new Coordinate(2.0, 1.1)
        );

        $this->assertEquals(
            [
                'top_left' => [
                    'lat' => 0.0,
                    'lon' => 1.1,
                ],
                'bottom_right' => [
                    'lat' => 2.0,
                    'lon' => 1.1,
                ],
            ],
            $square->toFilterArray()
        );
    }

    /**
     * Test from filter array.
     */
    public function testFromFilterArray()
    {
        $squareAsArray = [
            'top_left' => [
                'lat' => 0.0,
                'lon' => 1.1,
            ],
            'bottom_right' => [
                'lat' => 2.0,
                'lon' => 1.1,
            ],
        ];
        $square = Square::fromFilterArray($squareAsArray);

        $this->assertEquals(
            $squareAsArray,
            $square->toFilterArray()
        );
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $square = new Square(
            new Coordinate(0.0, 1.1),
            new Coordinate(2.0, 1.1)
        );

        $this->assertEquals(
            [
                'type' => 'Square',
                'data' => [
                    'top_left' => [
                        'lat' => 0.0,
                        'lon' => 1.1,
                    ],
                    'bottom_right' => [
                        'lat' => 2.0,
                        'lon' => 1.1,
                    ],
                ],
            ],
            $square->toArray()
        );
    }

    /**
     * Test from array.
     */
    public function testFromArray()
    {
        $squareAsArray = [
            'type' => 'Square',
            'data' => [
                'top_left' => [
                    'lat' => 0.0,
                    'lon' => 1.1,
                ],
                'bottom_right' => [
                    'lat' => 2.0,
                    'lon' => 1.1,
                ],
            ],
        ];
        $square = LocationRange::createFromArray($squareAsArray);

        $this->assertEquals(
            $squareAsArray,
            $square->toArray()
        );
    }
}
