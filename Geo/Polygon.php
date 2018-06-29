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

namespace Apisearch\Geo;

use Apisearch\Model\Coordinate;

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
    public function __construct(array $coordinates)
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
        return ['coordinates' => array_map(function (Coordinate $coordinate) {
            return $coordinate->toArray();
        }, $this->coordinates)];
    }

    /**
     * From filter array.
     *
     * @param array $array
     *
     * @return LocationRange
     */
    public static function fromFilterArray(array $array): LocationRange
    {
        $coordinates = array_map(function (array $coordinate) {
            return Coordinate::createFromArray($coordinate);
        }, $array['coordinates']);

        return new Polygon($coordinates);
    }
}
