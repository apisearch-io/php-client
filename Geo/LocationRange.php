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

use Puntmig\Search\Model\HttpTransportable;

/**
 * Interface LocationRange.
 */
abstract class LocationRange implements HttpTransportable
{
    /**
     * From filter array.
     *
     * @return array
     */
    abstract public function toFilterArray() : array;

    /**
     * From filter array.
     *
     * @param array $array
     */
    abstract public static function fromFilterArray(array $array);

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'type' => (new \ReflectionClass($this))->getShortName(),
            'data' => $this->toFilterArray(),
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array)
    {
        /**
         * @var $className static
         */
        $className = 'Puntmig\Search\Geo\\' . $array['type'];

        return $className::fromFilterArray($array['data']);
    }
}
