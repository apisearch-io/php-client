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

use Apisearch\Config\ImmutableConfig;
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
        $repository->createIndex(ImmutableConfig::createEmpty());
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $repository->flush();
        $this->assertEquals(
            '1',
            $repository
                ->query(Query::createByUUID(ItemUUID::createByComposedUUID('1~product')))
                ->getFirstItem()
                ->getId()
        );

        $repository->setRepositoryReference(RepositoryReference::create('yyy', 'yyy'));
        $repository->createIndex(ImmutableConfig::createEmpty());
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('2~product')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('3~roduct')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('4~product')));
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
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('5~product')));
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
                    $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteItem(ItemUUID::createByComposedUUID('1~product'));
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteIndex();
                },
            ],
            [
                function (Repository $repository) {
                    $repository->deleteItems([ItemUUID::createByComposedUUID('1~product')]);
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
        $repository->createIndex(ImmutableConfig::createEmpty());
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
                    $repository->createIndex(ImmutableConfig::createEmpty());
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
        $repository->createIndex(ImmutableConfig::createEmpty());
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
     * Test reset.
     */
    public function testReset()
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(ImmutableConfig::createEmpty());
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('1~product')));
        $repository->flush();
        $repository->resetIndex();
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
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(ImmutableConfig::createEmpty());
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
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(ImmutableConfig::createEmpty());
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
