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

namespace Puntmig\Search\Tests\Query;

use PHPUnit_Framework_TestCase;

use Puntmig\Search\Query\Query;

/**
 * Class QueryTest.
 */
class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test to array.
     */
    public function testToArray()
    {
        $queryArray = Query::createMatchAll()->toArray();
        $this->assertFalse(array_key_exists('coordinate', $queryArray));
        $this->assertFalse(array_key_exists('filters', $queryArray));
        $this->assertFalse(array_key_exists('aggregations', $queryArray));
        $this->assertFalse(array_key_exists('filter_fields', $queryArray));
    }
}
