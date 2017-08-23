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

namespace Puntmig\Search\Query;

/**
 * Class Range.
 */
class Range
{
    /**
     * @var int
     *
     * zero
     */
    const ZERO = 0;

    /**
     * @var int
     *
     * Infinite
     */
    const INFINITE = -1;

    /**
     * @var string
     *
     * Empty range
     */
    const SEPARATOR = '..';

    /**
     * Get values given string.
     *
     * @param string $string
     *
     * @return array
     */
    public static function stringToArray(string $string): array
    {
        list($from, $to) = explode(self::SEPARATOR, $string);
        $from = empty($from)
            ? self::ZERO
            : (is_numeric($from)
                ? (int) $from
                : $from);

        $to = empty($to)
            ? self::INFINITE
            : (is_numeric($to)
                ? (int) $to
                : $to);

        return [$from, $to];
    }

    /**
     * Get string given values.
     *
     * @param array $values
     *
     * @return string
     */
    public static function arrayToString(array $values): string
    {
        list($from, $to) = $values;
        $string = '';
        if ($from > self::ZERO) {
            $string .= $from;
        }

        $string .= self::SEPARATOR;

        if ($to >= self::ZERO) {
            $string .= $to;
        }

        return $string;
    }

    /**
     * Create a set of ranges given a minimum, a maximum and an incremental.
     *
     * @param int $from
     * @param int $to
     * @param int $incremental
     *
     * @return array
     */
    public static function createRanges(
        int $from,
        int $to,
        int $incremental
    ): array {
        $ranges = [];
        while ($from < $to) {
            $nextTo = $from + $incremental;
            $ranges[] = "$from..$nextTo";
            $from = $nextTo;
        }

        return $ranges;
    }
}
