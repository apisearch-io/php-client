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

use Puntmig\Search\Model\Coordinate;

/**
 * Class CoordinateAndDistance.
 */
class CoordinateAndDistance extends LocationRange
{
    /**
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var string
     *
     * Distance
     */
    private $distance;

    /**
     * CoordinateAndDistance constructor.
     *
     * @param Coordinate $coordinate
     * @param string     $distance
     */
    public function __construct(
        Coordinate $coordinate,
        string $distance
    ) {
        $this->coordinate = $coordinate;
        $this->distance = $distance;
    }

    /**
     * To filter array.
     *
     * @return array
     */
    public function toFilterArray(): array
    {
        return [
            'distance' => $this->distance,
            'coordinate' => $this
                ->coordinate
                ->toArray(),
        ];
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
        return new self(
            Coordinate::createFromArray($array['coordinate']),
            (string) $array['distance']
        );
    }
}
