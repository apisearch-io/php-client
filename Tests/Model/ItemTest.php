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

use Apisearch\Model\Coordinate;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use PHPUnit_Framework_TestCase;

/**
 * File header placeholder.
 */
class ItemTest extends PHPUnit_Framework_TestCase
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
     * Test item creation with location from array without coordinate in the
     * array.
     */
    public function testCreateLocatedFromArrayWithoutCoordinate()
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
    }

    /**
     * Test create item with bad formatted UUID.
     *
     * @dataProvider dataItemBadFormattedUUID
     *
     * @expectedException \Apisearch\Exception\UUIDException
     */
    public function testItemBadFormattedUUID($data)
    {
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
     *
     * @expectedException \Apisearch\Exception\CoordinateException
     */
    public function testCoordinateFormattedUUID($data)
    {
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
     * Test item to array.
     */
    public function testToArray()
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
}
