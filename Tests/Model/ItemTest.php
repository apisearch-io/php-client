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

namespace Apisearch\Tests\Model;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\AppUUID;
use Apisearch\Model\Coordinate;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * File header placeholder.
 */
class ItemTest extends TestCase
{
    /**
     * Test item creation with location.
     */
    public function testCreateLocated()
    {
        $item = Item::createLocated(
            new ItemUUID('1', 'product'),
            new Coordinate(0.0, 0.0),
            [],
            [],
            []
        );

        $this->assertNotNull($item->getCoordinate());
    }

    /**
     * Test item creation with location from array.
     */
    public function testCreateLocatedFromArray()
    {
        $itemArray = [
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'coordinate' => [
                'lon' => 0.0,
                'lat' => 0.0,
            ],
        ];

        $item = Item::createFromArray($itemArray);
        $this->assertNotNull($item->getCoordinate());
    }

    /**
     * Test create non located.
     */
    public function testCreate()
    {
        $item = Item::create(
            new ItemUUID('1', 'product'),
            [],
            [],
            []
        );

        $this->assertNull($item->getCoordinate());
        $this->assertSame(
            '1',
            $item->getUUID()->getId()
        );
        $this->assertSame('1', $item->getId());
        $this->assertSame('product', $item->getType());
    }

    /**
     * Test item creation from array without coordinate in the
     * array.
     */
    public function testCreateFromArrayWithoutCoordinate()
    {
        $itemArray = [
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
        ];

        $item = Item::createFromArray($itemArray);
        $this->assertNull($item->getCoordinate());
    }

    /**
     * Test create empty values.
     */
    public function testCreateEmptyValues()
    {
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'coordinate' => null,
            'distance' => null,
            'metadata' => [],
            'indexed_metadata' => [],
            'searchable_metadata' => [],
            'exact_matching_metadata' => [],
            'suggest' => [],
        ]);

        $this->assertSame([], $item->getMetadata());
        $this->assertSame([], $item->getIndexedMetadata());
        $this->assertSame([], $item->getSearchableMetadata());
        $this->assertSame([], $item->getExactMatchingMetadata());
        $this->assertSame([], $item->getSuggest());
        $this->assertSame([], $item->getAllMetadata());
        $this->assertNull($item->getCoordinate());
        $this->assertNull($item->getDistance());
        $this->assertNull($item->getScore());
    }

    /**
     * Test create item with bad formatted UUID.
     *
     * @dataProvider dataItemBadFormattedUUID
     */
    public function testItemBadFormattedUUID($data)
    {
        $this->expectException(InvalidFormatException::class);
        Item::createFromArray($data);
    }

    /**
     * Generate bad item UUID formats.
     */
    public function dataItemBadFormattedUUID()
    {
        return [
            [[]],
            [['id' => []]],
            [['id' => '1', 'type' => 'product']],
            [['uuid' => false]],
            [['uuid' => null]],
            [['uuid' => true]],
            [['uuid' => '']],
            [['uuid' => []]],
            [['uuid' => ['id' => '10']]],
            [['uuid' => ['type' => 'product']]],
        ];
    }

    /**
     * Test create Coordinate with bad formatted.
     *
     * @dataProvider dataCoordinateBadFormattedUUID
     */
    public function testCoordinateFormattedUUID($data)
    {
        $this->expectException(InvalidFormatException::class);
        Item::createFromArray(array_merge(['uuid' => [
                'id' => '1',
                'type' => 'product',
            ]],
            $data
        ));
    }

    /**
     * Generate bad Coordinate formats.
     */
    public function dataCoordinateBadFormattedUUID()
    {
        return [
            [['coordinate' => false]],
            [['coordinate' => true]],
            [['coordinate' => '']],
            [['coordinate' => []]],
            [['coordinate' => ['lat' => '10']]],
            [['coordinate' => ['lon' => '11']]],
        ];
    }

    /**
     * Test distance injection.
     */
    public function testDistanceInjection()
    {
        $itemArray = [
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'distance' => 0.1,
        ];

        $item = Item::createFromArray($itemArray);
        $this->assertSame($item->getDistance(), 0.1);
    }

    /**
     * Test distance injection without distance in the array.
     */
    public function testDistanceInjectionWithoutDistance()
    {
        $itemArray = [
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
        ];

        $item = Item::createFromArray($itemArray);
        $this->assertNull($item->getDistance());
    }

    /**
     * Test metadata.
     */
    public function testMetadata()
    {
        $metadata = [
            'a' => '1',
            'b' => 2,
        ];
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'metadata' => $metadata,
        ]);

        $this->assertSame($metadata, $item->getMetadata());
        $this->assertSame($metadata, $item->getAllMetadata());
        $this->assertSame('1', $item->get('a'));
        $this->assertSame(2, $item->get('b'));

        $item->setMetadata(['c' => true, 'd' => ['e' => 'hola']]);
        $this->assertEquals('hola', $item->getMetadata()['d']['e']);
        $this->assertEquals('hola', $item->getAllMetadata()['d']['e']);
        $this->assertEquals('hola', $item->get('d')['e']);
        $this->assertCount(2, $item->getAllMetadata());

        $item->addMetadata('z', 10);
        $this->assertCount(3, $item->getAllMetadata());
        $this->assertEquals(10, $item->getMetadata()['z']);
    }

    /**
     * Test indexedMetadata.
     */
    public function testIndexedMetadata()
    {
        $indexedMetadata = [
            'a' => '1',
            'b' => 2,
        ];
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'indexed_metadata' => $indexedMetadata,
        ]);

        $this->assertSame($indexedMetadata, $item->getIndexedMetadata());
        $this->assertSame($indexedMetadata, $item->getAllMetadata());
        $this->assertSame('1', $item->get('a'));
        $this->assertSame(2, $item->get('b'));

        $item->setIndexedMetadata(['c' => true, 'd' => ['e' => 'hola']]);
        $this->assertEquals('hola', $item->getIndexedMetadata()['d']['e']);
        $this->assertEquals('hola', $item->getAllMetadata()['d']['e']);
        $this->assertCount(2, $item->getAllMetadata());

        $item->addIndexedMetadata('z', 10);
        $this->assertCount(3, $item->getAllMetadata());
        $this->assertEquals(10, $item->getIndexedMetadata()['z']);
    }

    /**
     * Test all metadata priorities.
     */
    public function testAllMetadataPriorities()
    {
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'metadata' => ['a' => 'Hola'],
            'indexed_metadata' => ['a' => 'hola'],
        ]);
        $this->assertEquals('Hola', $item->getAllMetadata()['a']);
    }

    /**
     * Test item to array with default values.
     */
    public function testToArrayDefaultValues()
    {
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'coordinate' => null,
            'distance' => null,
            'is_promoted' => false,
            'metadata' => [],
            'indexed_metadata' => [],
            'searchable_metadata' => [],
            'exact_matching_metadata' => [],
            'suggest' => [],
        ]);
        $itemArray = $item->toArray();
        $this->assertSame(
            ['id' => '1', 'type' => 'product'],
            $itemArray['uuid']
        );
        $this->assertFalse(array_key_exists('coordinate', $itemArray));
        $this->assertFalse(array_key_exists('distance', $itemArray));
        $this->assertFalse(array_key_exists('metadata', $itemArray));
        $this->assertFalse(array_key_exists('indexed_metadata', $itemArray));
        $this->assertFalse(array_key_exists('searchable_metadata', $itemArray));
        $this->assertFalse(array_key_exists('exact_matching_metadata', $itemArray));
        $this->assertFalse(array_key_exists('suggest', $itemArray));
        $this->assertFalse(array_key_exists('is_promoted', $itemArray));
    }

    /**
     * Test item to array with all values.
     */
    public function testToArrayAllValues()
    {
        $itemAsArray = [
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'coordinate' => [
                'lat' => 0.0,
                'lon' => 0.0,
            ],
            'distance' => 0.4,
            'metadata' => ['a' => 1],
            'indexed_metadata' => ['b' => 2],
            'searchable_metadata' => ['c' => 3],
            'exact_matching_metadata' => ['d'],
            'suggest' => ['hola'],
            'is_promoted' => true,
            'highlights' => ['a' => '<strong>a</strong>'],
        ];

        $this->assertEquals(
            $itemAsArray,
            Item::createFromArray($itemAsArray)->toArray()
        );
    }

    /**
     * Test magic get method.
     */
    public function testMagicGetter()
    {
        $item = Item::createFromArray([
            'uuid' => [
                'id' => '1',
                'type' => 'product',
            ],
            'metadata' => [
                'a' => 1,
                'b' => true,
            ],
            'indexed_metadata' => [
                'c' => 2,
                'd' => [
                    'e' => 'z',
                ],
            ],
        ]);

        $this->assertEquals(1, $item->a);
        $this->assertEquals(true, $item->b);
        $this->assertEquals(2, $item->c);
        $this->assertEquals('z', $item->d['e']);
        $this->assertNull($item->nonexisting);
    }

    /**
     * Test composed uuid.
     */
    public function testComposedUUID()
    {
        $composedId = '1~product';
        $this->assertEquals(
            $composedId,
            Item::create(ItemUUID::createByComposedUUID($composedId))->composeUUID()
        );
    }

    /**
     * Test score.
     */
    public function testScore()
    {
        $item = Item::create(ItemUUID::createByComposedUUID('1~item'));
        $this->assertNull($item->getScore());
        $this->assertFalse(array_key_exists('score', $item->toArray()));
        $this->assertNull(Item::createFromArray($item->toArray())->getScore());

        $item = Item::create(ItemUUID::createByComposedUUID('1~item'))->setScore(5.5);
        $this->assertEquals(5.5, $item->getScore());
        $this->assertEquals(5.5, $item->toArray()['score']);
        $this->assertEquals(5.5, Item::createFromArray($item->toArray())->getScore());
    }

    /**
     * Test index and app UUID.
     */
    public function testIndexAndAppUUID()
    {
        $appUUID = AppUUID::createById('app1');
        $indexUUID = IndexUUID::createById('index1');
        $repositoryReference = RepositoryReference::create($appUUID, $indexUUID);

        $item = Item::create(ItemUUID::createByComposedUUID('1~item'));
        $this->assertNull($item->getAppUUID());
        $this->assertNull($item->getIndexUUID());
        $item = Item::createFromArray($item->toArray());
        $this->assertNull($item->getAppUUID());
        $this->assertNull($item->getIndexUUID());

        $item = Item::create(ItemUUID::createByComposedUUID('1~item'));
        $item->setRepositoryReference($repositoryReference);
        $this->assertEquals(
            $repositoryReference->getIndexUUID(),
            $item->getIndexUUID()
        );
        $this->assertEquals(
            $repositoryReference->getAppUUID(),
            $item->getAppUUID()
        );
        $item = Item::createFromArray($item->toArray());
        $this->assertEquals(
            $repositoryReference->getAppUUID(),
            $item->getAppUUID()
        );
        $this->assertEquals(
            $repositoryReference->getAppUUID(),
            $item->getAppUUID()
        );
    }

    /**
     * Test delete methods.
     */
    public function testDeleteMethods()
    {
        $item = Item::createFromArray([
            'uuid' => ['id' => 'A', 'type' => 'B'],
            'metadata' => ['A' => 1],
            'indexed_metadata' => ['B' => 2],
            'searchable_metadata' => ['C' => 3],
            'exact_matching_metadata' => ['D' => 4],
        ]);

        $item->deleteMetadata('A');
        $item->deleteIndexedMetadata('B');
        $item->deleteSearchableMetadata('C');
        $item->deleteExactMatchingMetadata('D');

        $this->assertEquals(['uuid' => ['id' => 'A', 'type' => 'B']], $item->toArray());
    }

    public function testMap()
    {
        $item = Item::createFromArray([
            'uuid' => ['id' => 'A', 'type' => 'B'],
            'metadata' => ['A' => 1],
            'indexed_metadata' => ['B' => 2],
            'searchable_metadata' => ['C' => 3],
            'exact_matching_metadata' => ['D' => 4],
            'suggest' => [5],
            'highlights' => ['F' => 6],
            'is_promoted' => true,
            'coordinate' => ['lat' => 1, 'lon' => 2],
        ]);

        $item->map(function (array $map) {
            $map['metadata']['A'] *= 2;
            $map['indexed_metadata']['B'] *= 2;
            $map['searchable_metadata']['C'] *= 2;
            $map['exact_matching_metadata']['D'] *= 2;
            $map['suggest'][0] *= 2;
            $map['highlights']['F'] *= 2;
            $map['is_promoted'] = false;
            $map['score'] = 10;
            $map['coordinate'] = null;

            return $map;
        });

        $this->assertEquals(2, $item->getMetadata()['A']);
        $this->assertEquals(4, $item->getIndexedMetadata()['B']);
        $this->assertEquals(6, $item->getSearchableMetadata()['C']);
        $this->assertEquals(8, $item->getExactMatchingMetadata()['D']);
        $this->assertEquals(10, $item->getSuggest()[0]);
        $this->assertEquals(12, $item->getHighlight('F'));
        $this->assertFalse($item->isPromoted());
        $this->assertEquals(10, $item->getScore());
        $this->assertNull($item->getCoordinate());

        $item->map(function (array $map) {
            $map['coordinate'] = ['lat' => 1, 'lon' => 2];

            return $map;
        });
        $this->assertInstanceOf(Coordinate::class, $item->getCoordinate());
    }

    /**
     * @return void
     */
    public function testPathByField()
    {
        $this->assertEquals('_id', Item::getPathByField('_id'));
        $this->assertEquals('_id', Item::getPathByField('uuid'));
        $this->assertEquals('uuid.id', Item::getPathByField('id'));
        $this->assertEquals('uuid.type', Item::getPathByField('type'));
        $this->assertEquals('indexed_metadata.another_id', Item::getPathByField('another_id'));
        $this->assertEquals('indexed_metadata._field', Item::getPathByField('_field'));
        $this->assertEquals('indexed_metadata._field', Item::getPathByField('indexed_metadata._field'));
    }
}
