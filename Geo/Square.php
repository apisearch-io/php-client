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
 * Class Square.
 */
class Square extends LocationRange
{
    /**
     * @var Coordinate
     *
     * Top left coordinate
     */
    private $topLeftCoordinate;

    /**
     * @var Coordinate
     *
     * Bottom right coordinate
     */
    private $bottomRightCoordinate;

    /**
     * SquareArea constructor.
     *
     * @param Coordinate $topLeftCoordinate
     * @param Coordinate $bottomRightCoordinate
     */
    public function __construct(
        Coordinate $topLeftCoordinate,
        Coordinate $bottomRightCoordinate
    ) {
        $this->topLeftCoordinate = $topLeftCoordinate;
        $this->bottomRightCoordinate = $bottomRightCoordinate;
    }

    /**
     * To filter array.
     *
     * @return array
     */
    public function toFilterArray(): array
    {
        return [
            'top_left' => $this
                ->topLeftCoordinate
                ->toArray(),
            'bottom_right' => $this
                ->bottomRightCoordinate
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
            Coordinate::createFromArray($array['top_left']),
            Coordinate::createFromArray($array['bottom_right'])
        );
    }
}
