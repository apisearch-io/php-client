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

use Apisearch\Model\IndexUUID;
use Apisearch\Query\Query;
use Apisearch\Query\ScoreStrategies;
use Apisearch\Query\ScoreStrategy;
use Apisearch\Query\SortBy;
use Apisearch\Tests\HttpHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryTest.
 */
class QueryTest extends TestCase
{
    /**
     * Test to array.
     */
    public function testToArray()
    {
        $queryArray = Query::createMatchAll()->toArray();
        $this->assertEquals([], $queryArray);
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
        $this->assertEquals(Query::DEFAULT_SIZE, $query->getSize());
    }

    /**
     * Test defaults.
     */
    public function testDefaults()
    {
        $queryArray = Query::create('')->toArray();
        $query = Query::createFromArray($queryArray);

        $this->assertEquals([], $query->getFields());
        $this->assertFalse($query->areSuggestionsEnabled());
        $this->assertTrue($query->areAggregationsEnabled());
        $this->assertEquals('', $query->getQueryText());
        $this->assertEquals(Query::DEFAULT_PAGE, $query->getPage());
        $this->assertEquals(Query::DEFAULT_SIZE, $query->getSize());
        $this->assertEquals(SortBy::create(), $query->getSortBy());
        $this->assertNull($query->getUser());
        $this->assertNull($query->getScoreStrategies());
        $this->assertEquals(Query::NO_MIN_SCORE, $query->getMinScore());
        $this->assertEquals([], $query->getMetadata());
        $this->assertEquals($query, HttpHelper::emulateHttpTransport($query));
        $this->assertNull($query->getUUID());
        $this->assertNull($query->getIndexUUID());
        $this->assertEquals([], $query->getsearchableFields());
    }

    /**
     * Test score strategy object.
     */
    public function testScoreStrategyObject()
    {
        $query = Query::createMatchAll()->setScoreStrategies(ScoreStrategies::createEmpty());

        $this->assertInstanceOf(
            ScoreStrategies::class,
            $query->getScoreStrategies()
        );

        $query = Query::createFromArray($query->toArray());

        $this->assertInstanceOf(
            ScoreStrategies::class,
            $query->getScoreStrategies()
        );
    }

    /**
     * Test fuzziness.
     */
    public function testFuzziness()
    {
        $query = Query::createMatchAll();
        $this->assertNull($query->getFuzziness());
        $this->assertFalse(array_key_exists('fuzziness', $query->toArray()));
        $query->setFuzziness(1.0);
        $this->assertEquals(1.0, $query->getFuzziness());
        $this->assertEquals(1.0, $query->toArray()['fuzziness']);
        $this->assertEquals(1.0, Query::createFromArray(['fuzziness' => 1.0])->getFuzziness());
        $this->assertEquals(null, Query::createFromArray([])->getFuzziness());
        $this->assertInstanceOf(Query::class, $query->setFuzziness('1..3'));
        $this->assertEquals('AUTO', Query::createMatchAll()->setAutoFuzziness()->getFuzziness());
        $this->assertInstanceOf(Query::class, $query->setAutoFuzziness());
        $this->assertEquals($query, HttpHelper::emulateHttpTransport($query));
    }

    /**
     * Test min score.
     */
    public function testMinScore()
    {
        $query = Query::createMatchAll()->setMinScore(10.0);
        $this->assertEquals(10.0, $query->getMinScore());
        $this->assertEquals(10.0, $query->toArray()['min_score']);
        $this->assertEquals(10.0, Query::createFromArray(['min_score' => 10.0])->toArray()['min_score']);
    }

    /**
     * Test values.
     */
    public function testValues()
    {
        $query = Query::create('hola', 2, 3)
            ->setFields([
                'a',
                'b',
            ]);

        $this->assertEquals('hola', $query->getQueryText());
        $this->assertEquals(2, $query->getPage());
        $this->assertEquals(3, $query->getSize());
        $this->assertEquals(['a', 'b'], $query->getFields());

        /**
         * To array transformation.
         */
        $query = Query::createFromArray($query->toArray());

        $this->assertEquals('hola', $query->getQueryText());
        $this->assertEquals(2, $query->getPage());
        $this->assertEquals(3, $query->getSize());
        $this->assertEquals(['a', 'b'], $query->getFields());
        $this->assertEquals($query, HttpHelper::emulateHttpTransport($query));
    }

    /**
     * Test metadata.
     */
    public function testMetadata()
    {
        $query = Query::createMatchAll();
        $query->setMetadataValue('a', 'a1');
        $query->setMetadataValue('b', ['b1', 'b2']);
        $this->assertEquals([
            'a' => 'a1',
            'b' => ['b1', 'b2'],
        ], $query->getMetadata());
        $this->assertEquals($query, HttpHelper::emulateHttpTransport($query));
    }

    /**
     * Test subqueries.
     */
    public function testSubqueries()
    {
        $query = Query::createMatchAll();
        $query->addSubQuery('sub1', Query::create('sub1'));
        $query->addSubQuery('sub2', Query::create('sub2'));
        $query->addSubQuery('sub3', Query::create('sub3'));
        $query->addSubQuery('sub4', Query::createMatchAll());
        $this->assertCount(4, $query->getSubqueries());
        $subqueries = HttpHelper::emulateHttpTransport($query)->getSubqueries();
        $this->assertEquals('sub1', $subqueries['sub1']->getQueryText());
        $this->assertEquals('sub2', $subqueries['sub2']->getQueryText());
        $this->assertEquals('sub3', $subqueries['sub3']->getQueryText());
        $this->assertEquals('', $subqueries['sub4']->getQueryText());

        $query = Query::createMultiquery([
            'sub1' => Query::create('sub1'),
            'sub2' => Query::create('sub2'),
            'sub3' => Query::create('sub3'),
            'sub4' => Query::createMatchAll(),
        ]);
        $this->assertCount(4, $query->getSubqueries());
    }

    /**
     * Test identifier.
     */
    public function testIdentifier()
    {
        $query = Query::createMatchAll()->identifyWith('123');
        $this->assertEquals('123', $query->getUUID());
        $query = HttpHelper::emulateHttpTransport($query);
        $this->assertEquals('123', $query->getUUID());
    }

    /**
     * Test indexUUID.
     */
    public function testIndexUUID()
    {
        $indexUUID = IndexUUID::createById('123');
        $query = Query::createMatchAll()->forceIndexUUID($indexUUID);
        $this->assertEquals($indexUUID, $query->getIndexUUID());
        $this->assertEquals($indexUUID->toArray(), $query->toArray()['index_uuid']);
        $query = HttpHelper::emulateHttpTransport($query);
        $this->assertEquals($indexUUID, $query->getIndexUUID());
        $this->assertEquals($indexUUID->toArray(), $query->toArray()['index_uuid']);
    }

    /**
     * Test searchable fields.
     */
    public function testSearchableFields()
    {
        $query = Query::createMatchAll()->setsearchableFields(['field1']);
        $this->assertEquals(['field1'], $query->getsearchableFields());
        $this->assertEquals(['field1'], $query->toArray()['searchable_fields']);
        $query = HttpHelper::emulateHttpTransport($query);
        $this->assertEquals(['field1'], $query->getsearchableFields());
    }

    /**
     * Test add score strategy when empty.
     */
    public function testAddScoreStrategyWhenEmpty()
    {
        $query = Query::createMatchAll();
        $this->assertNull($query->getScoreStrategies());
        $query->addScoreStrategy(ScoreStrategy::createDefault());
        $this->assertInstanceof(ScoreStrategies::class, $query->getScoreStrategies());
        $this->assertCount(1, $query->getScoreStrategies()->getScoreStrategies());
    }
}
