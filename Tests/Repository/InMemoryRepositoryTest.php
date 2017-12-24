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

namespace Apisearch\Tests\Repository;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\InMemoryRepository;
use Apisearch\Repository\Repository;
use Apisearch\Repository\RepositoryReference;
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
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(null);
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->flush();
        $this->assertEquals(
            '1',
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('product~1')))
                ->getFirstItem()
                ->getId()
        );

        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'yyy'));
        $repository->createIndex(null);
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

        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
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
        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'yyy'));
        $this->assertCount(
            3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
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
     * Test index not available.
     *
     * @expectedException \Apisearch\Exception\ResourceNotAvailableException
     * @dataProvider dataIndexNotAvailable
     */
    public function testIndexNotAvailable($action)
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('aaa', 'a'));
        $action($repository);
    }

    /**
     * Data for.
     *
     * @return array
     */
    public function dataIndexNotAvailable(): array
    {
        return [
            [
                function (Repository $repository) {
                    $repository->query(Query::createMatchAll());
                },
            ],
            [
                function (Repository $repository) {
                    $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~3')));
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteItem(ItemUUID::createByComposedUUID('1~3'));
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteIndex();
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteItems([ItemUUID::createByComposedUUID('1~3')]);
                },
            ],
            [
                function (Repository $repository) {
                    $repository->resetIndex();
                },
            ],
            [
                function (Repository $repository) {
                    $repository->flush();
                },
            ],
        ];
    }

    /**
     * Test index exists.
     *
     * @expectedException \Apisearch\Exception\ResourceExistsException
     * @dataProvider dataIndexExists
     */
    public function testIndexExists($action)
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('aaa', 'a'));
        $repository->createIndex(null);
        $action($repository);
    }

    /**
     * Data for.
     *
     * @return array
     */
    public function dataIndexExists(): array
    {
        return [
            [
                function (Repository $repository) {
                    $repository->createIndex(null);
                },
            ],
        ];
    }

    /**
     * Test add and delete at the same time.
     */
    public function testAddDeleteAtTheSameTime()
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(null);
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
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(null);
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->flush();
        $repository->resetIndex();
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
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(null);
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
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(null);
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
