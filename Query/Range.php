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

namespace Apisearch\Query;

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
    const MINUS_INFINITE = null;

    /**
     * @var int
     *
     * Infinite
     */
    const INFINITE = null;

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
        $from = '' === $from
            ? self::MINUS_INFINITE
            : (is_numeric($from)
                ? (int) $from
                : $from);

        $to = '' === $to
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
        if (self::MINUS_INFINITE === $values[0]) {
            $values[0] = '';
        }
        if (self::INFINITE === $values[1]) {
            $values[1] = '';
        }

        return implode(self::SEPARATOR, $values);
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
            $ranges[] = $from.self::SEPARATOR.$nextTo;
            $from = $nextTo;
        }

        return $ranges;
    }
}
