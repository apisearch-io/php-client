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

namespace Apisearch\Tests\Result;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\InMemoryRepository;
use PHPUnit_Framework_TestCase;

/**
 * Class InMemoryRepositoryTest.
 */
class InMemoryRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test add, delete and query items by UUID.
     */
    public function testBasics()
    {
        $repository = new InMemoryRepository();
        $repository->setAppId('xxx');
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->flush();
        $this->assertEquals(
            '1',
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('product~1')))
                ->getFirstItem()
                ->getId()
        );

        $repository->setAppId('yyy');
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~2')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~3')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~4')));
        $repository->flush();
        $this->assertCount(
            3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setAppId('xxx');
        $this->assertCount(
            1,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~5')));
        $repository->flush();
        $this->assertCount(
            2,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setAppId('yyy');
        $this->assertCount(
            3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setAppId('zzz');
        $this->assertCount(
            0,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setAppId('xxx');
        $repository->deleteItem(ItemUUID::createByComposedUUID('product~1'));
        $repository->deleteItem(ItemUUID::createByComposedUUID('product~5'));
        $repository->flush();
        $this->assertEmpty(
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('product~1')))
                ->getItems()
        );
    }

    /**
     * Test add and delete at the same time.
     */
    public function testAddDeleteAtTheSameTime()
    {
        $repository = new InMemoryRepository();
        $repository->setAppId('xxx');
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->deleteItem(ItemUUID::createByComposedUUID('product~1'));
        $repository->flush();
        $this->assertEmpty(
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('product~1')))
                ->getItems()
        );
    }

    /**
     * Test reset.
     */
    public function testReset()
    {
        $repository = new InMemoryRepository();
        $repository->setAppId('xxx');
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->flush();
        $repository->reset(null);
        $this->assertEmpty(
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('product~1')))
                ->getItems()
        );
    }

    /**
     * Test query multiple.
     */
    public function testQueryMultiple()
    {
        $repository = new InMemoryRepository();
        $repository->setAppId('xxx');
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~2')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~3')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('article~1')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('article~2')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('article~3')));
        $repository->flush();
        $this->assertCount(
            4,
            $repository
                ->query(Query::createByUUIDs([
                    ItemUUID::createByComposedUUID('product~1'),
                    ItemUUID::createByComposedUUID('product~2'),
                    ItemUUID::createByComposedUUID('article~2'),
                    ItemUUID::createByComposedUUID('article~3'),
                ]))
                ->getItems()
        );

        $this->assertCount(
            2,
            $repository
                ->query(Query::createByUUIDs([
                    ItemUUID::createByComposedUUID('product~1'),
                    ItemUUID::createByComposedUUID('product~2'),
                    ItemUUID::createByComposedUUID('product~2'),
                ]))
                ->getItems()
        );
    }

    /**
     * Test invalid queries.
     *
     * @dataProvider dataInvalidQueries
     *
     * @expectedException \Exception
     */
    public function testInvalidQueries(Query $query)
    {
        $repository = new InMemoryRepository();
        $repository->setAppId('xxx');
        $repository->query($query);
    }

    /**
     * Data for testInvalidQueries.
     *
     * @return array
     */
    public function dataInvalidQueries()
    {
        return [
            [Query::createMatchAll()->filterByTypes(['article'])],
            [Query::createMatchAll()->filterBy('name', 'name', ['name1'])],
        ];
    }
}
