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

namespace Apisearch\Tests\Repository;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\Repository;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryTest.
 */
abstract class RepositoryTest extends TestCase
{
    /**
     * Get repository intance.
     *
     * @return Repository
     */
    abstract protected function getRepository(): Repository;

    /**
     * Test add, delete and query items by UUID.
     */
    public function testBasics()
    {
        $repository = $this->getRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $repository->flush();
        $this->assertEquals(
            '1',
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('1~product')))
                ->getFirstItem()
                ->getId()
        );

        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('yyy'), IndexUUID::createById('yyy')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('2~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('3~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('4~product')));
        $repository->flush();
        $this->assertCount(
            3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );

        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
        $this->assertCount(
            1,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('5~product')));
        $repository->flush();
        $this->assertCount(
            2,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('yyy'), IndexUUID::createById('yyy')));
        $this->assertCount(
            3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
        $repository->deleteItem(ItemUUID::createByComposedUUID('1~product'));
        $repository->deleteItem(ItemUUID::createByComposedUUID('5~product'));
        $repository->flush();
        $this->assertEmpty(
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('1~product')))
                ->getItems()
        );
    }

    /**
     * Test add and delete at the same time.
     */
    public function testAddDeleteAtTheSameTime()
    {
        $repository = $this->getRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $repository->deleteItem(ItemUUID::createByComposedUUID('1~product'));
        $repository->flush();
        $this->assertEmpty(
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('1~product')))
                ->getItems()
        );
    }

    /**
     * Test query multiple.
     */
    public function testQueryMultiple()
    {
        $repository = $this->getRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('2~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('3~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~article')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('2~article')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('3~article')));
        $repository->flush();
        $this->assertCount(
            4,
            $repository
                ->query(Query::createByUUIDs([
                    ItemUUID::createByComposedUUID('1~product'),
                    ItemUUID::createByComposedUUID('2~product'),
                    ItemUUID::createByComposedUUID('2~article'),
                    ItemUUID::createByComposedUUID('3~article'),
                ]))
                ->getItems()
        );

        $this->assertCount(
            2,
            $repository
                ->query(Query::createByUUIDs([
                    ItemUUID::createByComposedUUID('1~product'),
                    ItemUUID::createByComposedUUID('2~product'),
                    ItemUUID::createByComposedUUID('2~product'),
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
        $repository = $this->getRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
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
