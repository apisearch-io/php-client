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

namespace Puntmig\Search\Tests\Result;

use PHPUnit_Framework_TestCase;

use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

/**
 * File header placeholder.
 */
class ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test to array.
     */
    public function testToArray()
    {
        $result = new Result(
            Query::createMatchAll(),
            1, 1
        );
        $resultArray = $result->toArray();
        $this->assertFalse(array_key_exists('items', $resultArray));
        $this->assertFalse(array_key_exists('aggregations', $resultArray));
        $this->assertFalse(array_key_exists('suggests', $resultArray));
    }
}
