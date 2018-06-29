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

namespace Apisearch\Model;

use Apisearch\Exception\InvalidFormatException;

/**
 * Class Coordinate.
 */
class Coordinate implements HttpTransportable
{
    /**
     * @var float
     *
     * Latitude
     */
    private $latitude;

    /**
     * @var float
     *
     * Longitude
     */
    private $longitude;

    /**
     * GeoPoint constructor.
     *
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(
        float $latitude,
        float $longitude
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get latitude.
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'lat' => $this->latitude,
            'lon' => $this->longitude,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return Coordinate
     */
    public static function createFromArray(array $array): self
    {
        if (
            !isset($array['lat']) ||
            !isset($array['lon'])
        ) {
            throw InvalidFormatException::coordinateFormatNotValid();
        }

        return new self(
            $array['lat'],
            $array['lon']
        );
    }
}
