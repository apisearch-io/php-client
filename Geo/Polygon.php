<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Geo;

use ReflectionClass;

use Puntmig\Search\Model\Coordinate;

/**
 * Class Polygon.
 */
class Polygon extends LocationRange
{
    /**
     * @var array
     *
     * Coordinates
     */
    private $coordinates;

    /**
     * Polygon constructor.
     *
     * @param Coordinate[] $coordinates
     */
    public function __construct(Coordinate ...$coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * To filter array.
     *
     * @return array
     */
    public function toFilterArray(): array
    {
        return array_map(function (Coordinate $coordinate) {
            return $coordinate->toArray();
        }, $this->coordinates);
    }

    /**
     * From filter array.
     *
     * @param array $array
     *
     * @return LocationRange
     */
    public static function fromFilterArray(array $array) : LocationRange
    {
        $coordinates = array_map(function (array $coordinate) {
            return Coordinate::createFromArray($coordinate);
        }, $array);

        $class = new ReflectionClass(static::class);

        return $class->newInstanceArgs($coordinates);
    }
}
