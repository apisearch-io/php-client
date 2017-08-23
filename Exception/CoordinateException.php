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

namespace Puntmig\Search\Exception;

/**
 * Class CoordinateException.
 */
class CoordinateException extends ModelException
{
    /**
     * Create Coordinate bad format exception.
     *
     * @return CoordinateException
     */
    public static function createCoordinateBadFormatException(): CoordinateException
    {
        return new self('A Coordinate should always contain a lat (Latitude) and a lon (Longitude)');
    }
}
