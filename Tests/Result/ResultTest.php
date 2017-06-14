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

use Puntmig\Search\Model\Item;
use Puntmig\Search\Model\ItemUUID;
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

    /**
     * Test get items grouped by type.
     */
    public function testGetItemsGroupedByType()
    {
        $result = new Result(
            Query::createMatchAll(),
            1, 1
        );

        $result->addItem(Item::create(ItemUUID::createByComposedUUID('type1~1')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('type2~3')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('type3~1')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('type2~4')));
        $result->addItem(Item::create(ItemUUID::createByComposedUUID('type1~10')));

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
    }
}
