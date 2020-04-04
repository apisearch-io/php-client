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

namespace Apisearch\Tests\Query;

use Apisearch\Query\Range;
use PHPUnit\Framework\TestCase;

/**
 * Class RangeTest.
 */
class RangeTest extends TestCase
{
    /**
     * Test string to array.
     *
     * @param string $string
     * @param array  $result
     *
     * @dataProvider dataStringToArray
     */
    public function testStringToArray(string $string, array $result)
    {
        $this->assertEquals(
            $result,
            Range::stringToArray($string)
        );
    }

    /**
     * Data with string and array.
     *
     * @return array
     */
    public function dataStringToArray(): array
    {
        return [
            ['2..5', [2, 5]],
            ['..10', [Range::MINUS_INFINITE, 10]],
            ['2..', [2, Range::INFINITE]],
            ['0..100', [0, 100]],
            ['-100..0', [-100, 0]],
            ['..', [Range::MINUS_INFINITE, Range::INFINITE]],
        ];
    }

    /**
     * Test array to string.
     *
     * @param array  $array
     * @param string $result
     *
     * @dataProvider dataArrayToString
     */
    public function testArrayToString(array $array, string $result)
    {
        $this->assertEquals(
            $result,
            Range::arrayToString($array)
        );
    }

    /**
     * Data with string and array.
     *
     * @return array
     */
    public function dataArrayToString(): array
    {
        return [
            [[2, 6], '2..6'],
            [[Range::MINUS_INFINITE, 10], '..10'],
            [[2, Range::INFINITE], '2..'],
            [[Range::MINUS_INFINITE, Range::INFINITE], '..'],
            [[0, 100], '0..100'],
            [[-100, 0], '-100..0'],
        ];
    }

    /**
     * Test create ranges.
     */
    public function testCreateRanges()
    {
        $this->assertEquals(
            [
                '1..3',
                '3..5',
                '5..7',
            ],
            Range::createRanges(1, 7, 2)
        );

        $this->assertEquals(
            [
                '0..2',
                '2..4',
            ],
            Range::createRanges(0, 4, 2)
        );
    }
}
