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

namespace Apisearch\Tests\Model;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\Coordinate;
use PHPUnit\Framework\TestCase;

/**
 * Class CoordinateTest.
 */
class CoordinateTest extends TestCase
{
    /**
     * Test getters.
     */
    public function testGetters()
    {
        $coordinate = new Coordinate(1.20, 2.10);
        $this->assertSame(
            1.20,
            $coordinate->getLatitude()
        );
        $this->assertSame(
            2.10,
            $coordinate->getLongitude()
        );
    }

    /**
     * Test getters.
     */
    public function testCreateFromArray()
    {
        $coordinate = Coordinate::createFromArray([
            'lat' => 1.20,
            'lon' => 2.10,
        ]);
        $this->assertSame(
            1.20,
            $coordinate->getLatitude()
        );
        $this->assertSame(
            2.10,
            $coordinate->getLongitude()
        );
    }

    /**
     * Test construct from empty array.
     */
    public function testConstructFromArrayWithEmptyArray()
    {
        try {
            Coordinate::createFromArray([]);
            $this->fail('Coordinate should not be built with empty array');
        } catch (InvalidFormatException $e) {
            // Silent pass
            $this->assertTrue(true);
        }
    }

    /**
     * Test construct from array without latitude.
     */
    public function testConstructFromArrayWithoutLatitude()
    {
        try {
            Coordinate::createFromArray([
                'lon' => 2.10,
            ]);
            $this->fail('Coordinate should not be built without a latitude');
        } catch (InvalidFormatException $e) {
            // Silent pass
            $this->assertTrue(true);
        }
    }

    /**
     * Test construct from array without longitude.
     */
    public function testConstructFromArrayWithoutLongitude()
    {
        try {
            Coordinate::createFromArray([
                'lat' => 2.10,
            ]);
            $this->fail('Coordinate should not be built without a longitude');
        } catch (InvalidFormatException $e) {
            // Silent pass
            $this->assertTrue(true);
        }
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $coordinateAsArray = [
            'lat' => 1.20,
            'lon' => 2.10,
        ];
        $coordinate = Coordinate::createFromArray($coordinateAsArray);
        $this->assertEquals(
            $coordinateAsArray,
            $coordinate->toArray()
        );
    }
}
