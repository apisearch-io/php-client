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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Model;

/**
 * Class Metadata.
 */
class Metadata
{
    /**
     * array to metadata format.
     *
     * @param array $array
     *
     * @return string
     */
    public static function toMetadata(array $array): string
    {
        array_walk(
            $array,
            function (&$value, $key) {
                $value = "$key##$value";
            }
        );

        return implode(
            '~~',
            $array
        );
    }

    /**
     * metadata format to array.
     *
     * Allowed these formats
     *
     * "simpletext"
     * "id##1234~~name##Marc
     *
     * First format should cover both id and name with the desired value.
     *
     * @param string $metadata
     *
     * @return array|null
     */
    public static function fromMetadata(string $metadata): ? array
    {
        $values = [];
        $splittedParts = explode('~~', $metadata);

        foreach ($splittedParts as $part) {
            $parts = explode('##', $part);
            if (count($parts) > 1) {
                $values[$parts[0]] = $parts[1];
            } else {
                $values[] = $part;
            }
        }

        if (1 == count($values)) {
            $firstAndUniqueElement = reset($values);
            $values = [
                'id' => $firstAndUniqueElement,
                'name' => $firstAndUniqueElement,
            ];
        }

        if (!array_key_exists('id', $values)) {
            return null;
        }

        return $values;
    }
}
