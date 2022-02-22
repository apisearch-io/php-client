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

namespace Apisearch\Tests\Result;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Aggregation;
use Apisearch\Result\Aggregations;
use Apisearch\Result\Result;
use Apisearch\Tests\HttpHelper;
use PHPUnit\Framework\TestCase;

/**
 * File header placeholder.
 */
class ResultTest extends TestCase
{
    /**
     * Test to array.
     */
    public function testToArray()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );
        $resultArray = $result->toArray();
        $this->assertFalse(array_key_exists('items', $resultArray));
        $this->assertFalse(array_key_exists('aggregations', $resultArray));
        $this->assertFalse(array_key_exists('suggests', $resultArray));
        $this->assertEquals('123', $result->getQueryUUID());
        $this->assertEquals(1, $result->getTotalHits());
        $this->assertEquals(1, $resultArray['total_hits']);
        $this->assertEquals(2, $result->getTotalItems());
        $this->assertEquals(2, $resultArray['total_items']);
        $this->assertCount(0, $result->getItems());
        $this->assertNull($result->getFirstItem());
        $this->assertCount(0, $result->getSuggestions());
        $this->assertNull($result->getAggregations());
        $this->assertEquals($resultArray, Result::createFromArray($resultArray)->toArray());
    }

    /**
     * Test items.
     */
    public function testItems()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('2~product')));
        $resultArray = $result->toArray();
        $this->assertCount(2, $result->getItems());
        $this->assertCount(2, $resultArray['items']);
        $this->assertInstanceof(Item::class, $result->getFirstItem());
        $this->assertEquals('1', $resultArray['items'][0]['uuid']['id']);
    }

    /**
     * Test aggregations.
     */
    public function testAggregations()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );
        $aggregations = new Aggregations(2);
        $aggregations->addAggregation('blabla', new Aggregation('hola', 0, 10, ['hola']));
        $aggregations->addAggregation('empty', new Aggregation('empty', 0, 10, []));
        $result->setAggregations($aggregations);
        $resultArray = $result->toArray();
        $this->assertInstanceof(Aggregations::class, $result->getAggregations());
        $this->assertNotEmpty($resultArray['aggregations']);
        $this->assertInstanceof(Aggregation::class, $aggregations->getAggregation('blabla'));
        $this->assertInstanceof(Aggregation::class, $result->getAggregation('blabla'));
        $this->assertNull($result->getAggregation('adeu'));
        $this->assertTrue($result->hasNotEmptyAggregation('blabla'));
        $this->assertFalse($result->hasNotEmptyAggregation('empty'));
        $this->assertFalse($result->hasNotEmptyAggregation('adeu'));
    }

    /**
     * Test suggests.
     */
    public function testSuggests()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );
        $result->addSuggestion('hola');
        $resultArray = $result->toArray();
        $this->assertCount(1, $result->getSuggestions());
        $this->assertCount(1, $resultArray['suggests']);

        $result->addSuggestion('hola2');
        $this->assertCount(2, $result->getSuggestions());

        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );
        $resultAsArray = $result->toArray();
        $newResult = Result::createFromArray($resultAsArray);

        $this->assertEquals([], $result->getSuggestions());
        $this->assertArrayNotHasKey('suggests', $resultAsArray);
        $this->assertEquals([], $newResult->getSuggestions());
    }

    public function testAutocomplete()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            2, 1
        );

        $result->setAutocomplete('hola');
        $resultAsArray = $result->toArray();
        $newResult = Result::createFromArray($resultAsArray);

        $this->assertEquals('hola', $result->getAutocomplete());
        $this->assertEquals('hola', $resultAsArray['autocomplete']);
        $this->assertEquals('hola', $newResult->getAutocomplete());

        $result->setAutocomplete('');
        $resultAsArray = $result->toArray();
        $newResult = Result::createFromArray($resultAsArray);

        $this->assertEquals('', $result->getAutocomplete());
        $this->assertArrayNotHasKey('autocomplete', $resultAsArray);
        $this->assertEquals('', $newResult->getAutocomplete());
    }

    /**
     * Test get items grouped by type.
     */
    public function testGetItemsGroupedByType()
    {
        $result = new Result(
            Query::createMatchAll()->identifyWith('123'),
            1, 1
        );

        $result->addItem(Item::create(ItemUUID::createByComposedUUID('1~type1')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('3~type2')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('1~type3')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('4~type2')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('10~type1')));

        $this->assertCount(2, $result->getItemsByType('type1'));
        $this->assertCount(2, $result->getItemsByType('type2'));
        $this->assertCount(1, $result->getItemsByType('type3'));
        $this->assertCount(0, $result->getItemsByType('type-nonextisting'));
        $this->assertCount(3, $result->getItemsGroupedByTypes());
        $this->assertCount(2, $result->getItemsGroupedByTypes()['type1']);
        $this->assertCount(2, $result->getItemsGroupedByTypes()['type2']);
        $this->assertCount(1, $result->getItemsGroupedByTypes()['type3']);
        $this->assertFalse(array_key_exists(
            'type-nonextisting',
            $result->getItemsGroupedByTypes()
        ));

        $this->assertCount(4, $result->getItemsByTypes(['type1', 'type2']));
        $this->assertCount(3, $result->getItemsByTypes(['type1', 'type3']));
        $this->assertCount(2, $result->getItemsByTypes(['type1', 'type-nonextisting']));
        $this->assertCount(0, $result->getItemsByTypes(['type-nonextisting']));
    }

    /**
     * Test create from array.
     */
    public function testCreateFromArrayAllValues()
    {
        $resultAsArray = [
            'query_uuid' => '123',
            'total_items' => 10,
            'total_hits' => 20,
            'aggregations' => [
                'aggregations' => [
                    'gogo' => [
                        'name' => 'hola',
                    ],
                ],
            ],
            'suggests' => [
                'sug1',
                'sug2',
            ],
            'items' => [
                [
                    'uuid' => [
                        'id' => 1,
                        'type' => 'product',
                    ],
                ],
                [
                    'uuid' => [
                        'id' => 2,
                        'type' => 'product',
                    ],
                ],
            ],
        ];

        $result = Result::createFromArray($resultAsArray);
        $this->assertEquals('123', $result->getQueryUUID());
        $this->assertEquals(10, $result->getTotalItems());
        $this->assertEquals(20, $result->getTotalHits());
        $this->assertInstanceof(Aggregation::class, $result->getAggregation('gogo'));
        $this->assertEquals(['sug1', 'sug2'], $result->getSuggestions());
        $this->assertCount(2, $result->getItems());
        $this->assertEquals('product', $result->getFirstItem()->getType());
    }

    /**
     * Test multi result.
     */
    public function testMultiResult()
    {
        $result = Result::createMultiResult([
            'res1' => Result::create(Query::createMatchAll()->identifyWith('1'), 10, 3, null, [], []),
            'res2' => Result::create(Query::createMatchAll()->identifyWith('2'), 10, 4, null, [], []),
            'res3' => Result::create(Query::createMatchAll()->identifyWith('3'), 10, 5, null, [], []),
        ]);

        $this->assertCount(3, $result->getSubresults());
        $subqueries = HttpHelper::emulateHttpTransport($result)->getSubresults();
        $this->assertEquals(3, $subqueries['res1']->getTotalHits());
        $this->assertEquals(4, $subqueries['res2']->getTotalHits());
        $this->assertEquals(5, $subqueries['res3']->getTotalHits());
    }

    /**
     * Test suggests are a simple array.
     */
    public function testSuggest()
    {
        $result = new Result(Query::createMatchAll()->identifyWith('aa'), 1, 1);
        $result->addSuggestion('str1');
        $result->addSuggestion('str1');
        $result->addSuggestion('str2');

        $this->assertEquals(['str1', 'str2'], $result->getSuggestions());
        $this->assertEquals($result->getSuggestions(), $result->getSuggestions());
        $this->assertEquals(['str1', 'str2'], $result->toArray()['suggests']);
        $this->assertEquals(['str1', 'str2'], Result::createFromArray($result->toArray())->getSuggestions());
        $this->assertEquals(['str1', 'str2'], Result::createFromArray($result->toArray())->getSuggestions());

        $this->assertEquals('sugg1', Result::create(
            Query::createMatchAll()->identifyWith('sugg1'), 1, 1, null, [
                'sugg1',
            ], []
        )->getSuggestions()[0]);

        $this->assertEquals('sugg1', Result::create(
            Query::createMatchAll()->identifyWith('sugg1'), 1, 1, null, [
            'sugg1',
        ], []
        )->toArray()['suggests'][0]);
    }
}
