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
use Puntmig\Search\Query\SortBy;

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
        $this->assertFalse(array_key_exists('user', $queryArray));
    }

    /**
     * Test defaults.
     */
    public function testDefaultsInfinite()
    {
        $queryArray = Query::createMatchAll()->toArray();
        $query = Query::createFromArray($queryArray);

        $this->assertFalse($query->areSuggestionsEnabled());
        $this->assertTrue($query->areAggregationsEnabled());
        $this->assertEquals('', $query->getQueryText());
        $this->assertEquals(Query::DEFAULT_PAGE, $query->getPage());
        $this->assertEquals(Query::INFINITE_SIZE, $query->getSize());
    }

    /**
     * Test defaults.
     */
    public function testDefaults()
    {
        $queryArray = Query::create('')->toArray();
        $query = Query::createFromArray($queryArray);

        $this->assertFalse($query->areSuggestionsEnabled());
        $this->assertTrue($query->areAggregationsEnabled());
        $this->assertEquals('', $query->getQueryText());
        $this->assertEquals(Query::DEFAULT_PAGE, $query->getPage());
        $this->assertEquals(Query::DEFAULT_SIZE, $query->getSize());
        $this->assertEquals(SortBy::SCORE, $query->getSortBy());
        $this->assertNull($query->getUser());
    }
}
