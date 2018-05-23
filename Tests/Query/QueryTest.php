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

namespace Apisearch\Tests\Query;

use Apisearch\Query\Query;
use Apisearch\Query\ScoreStrategy;
use Apisearch\Query\SortBy;
use PHPUnit_Framework_TestCase;

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
        $this->assertFalse(array_key_exists('score_strategy', $queryArray));
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
        $this->assertNull($query->getScoreStrategy());
    }

    /**
     * Test score strategy object.
     */
    public function testScoreStrategyObject()
    {
        $function = 'xxx';
        $scoreStrategy = ScoreStrategy::createCustomFunction($function);
        $query = Query::createMatchAll()->setScoreStrategy($scoreStrategy);

        $this->assertInstanceOf(
            ScoreStrategy::class,
            $query->getScoreStrategy()
        );

        $query = Query::createFromArray($query->toArray());

        $scoreStrategy = $query->getScoreStrategy();
        $this->assertInstanceOf(
            ScoreStrategy::class,
            $scoreStrategy
        );

        $this->assertEquals(
            ScoreStrategy::CUSTOM_FUNCTION,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            $function,
            $scoreStrategy->getFunction()
        );
    }
}
