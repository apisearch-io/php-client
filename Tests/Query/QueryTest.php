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
use Apisearch\Query\Aggregation;
use Apisearch\Query\Filter;
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

        $this->assertEquals(0, $query->getNumberOfSuggestions());
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
        $this->assertEquals(0, $query->getNumberOfSuggestions());
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
        $this->assertEquals(Query::QUERY_OPERATOR_OR, $query->getQueryOperator());
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

    public function testQueryTextWithTags()
    {
        $query = Query::create('<a>hola  </a>');
        $this->assertEquals('hola', $query->getQueryText());

        $query = Query::create('<pre>hola</pre><br>');
        $this->assertEquals('hola', $query->getQueryText());

        $query = Query::createFromArray([
            'q' => '<a>hola  </a>',
        ]);
        $this->assertEquals('hola', $query->getQueryText());
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
        $this->assertNull($query->getMetadataValue('lolazo'));
        $this->assertEquals('a1', $query->getMetadataValue('a'));
        $this->assertEquals(['b1', 'b2'], $query->getMetadataValue('b'));
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

    public function testSuggestions()
    {
        $query = Query::createMatchAll();
        $query->setNumberOfSuggestions(10);
        $queryAsArray = $query->toArray();
        $newQuery = Query::createFromArray($queryAsArray);

        $this->assertEquals(10, $query->getNumberOfSuggestions());
        $this->assertEquals(10, $queryAsArray['number_of_suggestions']);
        $this->assertEquals(10, $newQuery->getNumberOfSuggestions());
    }

    public function testAutocomplete()
    {
        $query = Query::createMatchAll();
        $query->enableAutocomplete();
        $queryAsArray = $query->toArray();
        $newQuery = Query::createFromArray($queryAsArray);

        $this->assertTrue($query->isAutocompleteEnabled());
        $this->assertTrue($queryAsArray['autocomplete_enabled']);
        $this->assertTrue($newQuery->isAutocompleteEnabled());
    }

    public function testQueryOperator()
    {
        $query = Query::createMatchAll()->setQueryOperator(Query::QUERY_OPERATOR_OR);
        $queryAsArray = $query->toArray();
        $newQuery = Query::createFromArray($queryAsArray);

        $this->assertEquals(Query::QUERY_OPERATOR_OR, $query->getQueryOperator());
        $this->assertEquals(Query::QUERY_OPERATOR_OR, $query->getQueryOperator());
        $this->assertEquals(Query::QUERY_OPERATOR_OR, $newQuery->getQueryOperator());

        $query = Query::createMatchAll()->setQueryOperator(Query::QUERY_OPERATOR_AND);
        $queryAsArray = $query->toArray();
        $newQuery = Query::createFromArray($queryAsArray);

        $this->assertEquals(Query::QUERY_OPERATOR_AND, $query->getQueryOperator());
        $this->assertEquals(Query::QUERY_OPERATOR_AND, $queryAsArray['query_operator']);
        $this->assertEquals(Query::QUERY_OPERATOR_AND, $newQuery->getQueryOperator());
    }

    public function testSetQueryText()
    {
        $query = Query::createMatchAll()->setQueryText('lolazo');
        $queryAsArray = $query->toArray();
        $newQuery = Query::createFromArray($queryAsArray);

        $this->assertEquals('lolazo', $query->getQueryText());
        $this->assertEquals('lolazo', $queryAsArray['q']);
        $this->assertEquals('lolazo', $newQuery->getQueryText());
    }

    public function testAggregationPromoted()
    {
        $query = Query::createMatchAll()
            ->aggregateBy('field1', 'field1', Filter::AT_LEAST_ONE, Aggregation::SORT_BY_COUNT_ASC, 0, ['item1', 'item2'])
            ->aggregateByRange('field2', 'field2', ['op1'], Filter::AT_LEAST_ONE, Filter::TYPE_RANGE, Aggregation::SORT_BY_COUNT_ASC, 0, ['item1', 'item3'])
            ->aggregateByDateRange('field3', 'field3', ['op2'], Filter::AT_LEAST_ONE, Aggregation::SORT_BY_COUNT_ASC, 0, ['item4']);

        $this->assertEquals(['item1', 'item2'], $query->getAggregation('field1')->getPromoted());
        $this->assertEquals(['item1', 'item3'], $query->getAggregation('field2')->getPromoted());
        $this->assertEquals(['item4'], $query->getAggregation('field3')->getPromoted());
    }

    public function testDeleteAggregationByField()
    {
        $query = Query::createMatchAll()
            ->aggregateBy('field1', 'field1', Filter::AT_LEAST_ONE, Aggregation::SORT_BY_COUNT_ASC)
            ->aggregateBy('field2', 'field2', Filter::AT_LEAST_ONE, Aggregation::SORT_BY_COUNT_ASC);

        $this->assertCount(2, $query->getAggregations());
        $this->assertNotNull($query->getAggregation('field1'));
        $this->assertNotNull($query->getAggregation('field2'));
        $this->assertNull($query->getAggregation('field3'));

        $query->deleteAggregationByField('field1');
        $this->assertCount(1, $query->getAggregations());
        $this->assertNull($query->getAggregation('field1'));
        $this->assertNotNull($query->getAggregation('field2'));
        $this->assertNull($query->getAggregation('field3'));

        $query->deleteAggregationByField('field1');
        $this->assertCount(1, $query->getAggregations());
        $this->assertNull($query->getAggregation('field1'));
        $this->assertNotNull($query->getAggregation('field2'));
        $this->assertNull($query->getAggregation('field3'));

        $query->deleteAggregationByField('field2');
        $this->assertCount(0, $query->getAggregations());
        $this->assertNull($query->getAggregation('field1'));
        $this->assertNull($query->getAggregation('field2'));
        $this->assertNull($query->getAggregation('field3'));

        $query->deleteAggregationByField('field3');
        $this->assertCount(0, $query->getAggregations());
        $this->assertNull($query->getAggregation('field1'));
        $this->assertNull($query->getAggregation('field2'));
        $this->assertNull($query->getAggregation('field3'));
    }

    public function testForceSizeAndPage()
    {
        $query = Query::create('x', 1, 10);
        $this->assertEquals(10, $query->getSize());
        $this->assertEquals(0, $query->getFrom());
        $query->forceSize(7);
        $this->assertEquals(7, $query->getSize());
        $this->assertEquals(0, $query->getFrom());

        $query = Query::create('x', 2, 5);
        $this->assertEquals(2, $query->getPage());
        $this->assertEquals(5, $query->getSize());
        $this->assertEquals(5, $query->getFrom());

        $query->forcePage(3);
        $this->assertEquals(3, $query->getPage());
        $this->assertEquals(10, $query->getFrom());
    }

    public function testContext()
    {
        $this->assertEmpty(Query::createMatchAll()->getContext());
        $this->assertEmpty((Query::createMatchAll())->toArray()['context'] ?? []);
        $query = Query::createMatchAll()->setContext(['context1']);
        $queryAsArray = [
            'context' => ['context1'],
        ];
        $this->assertEquals($queryAsArray, $query->toArray());
        $this->assertEquals(['context1'], $query->getContext());
        $this->assertEquals($queryAsArray, Query::createFromArray($query->toArray())->toArray());
    }
}
