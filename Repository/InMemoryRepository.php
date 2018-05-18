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

namespace Apisearch\Repository;

use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Filter;
use Apisearch\Query\Query;
use Apisearch\Result\Result;
use LogicException;

/**
 * Class InMemoryRepository.
 */
class InMemoryRepository extends Repository
{
    /**
     * @var array
     *
     * Items
     */
    private $items = [];

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    public function query(Query $query): Result
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $resultingItems = $this->items[$this->getIndexKey()];

        if (!empty($query->getFilters())) {
            foreach ($query->getFilters() as $filter) {
                if (Filter::TYPE_QUERY === $filter->getFilterType() && $filter->getValues() === ['']) {
                    continue;
                }

                if ('_id' !== $filter->getField()) {
                    throw new LogicException('Queries by field different than UUID not allowed in InMemoryRepository. Only for testing purposes.');
                }

                $resultingItems = [];
                $itemUUIDs = $filter->getValues();
                foreach ($itemUUIDs as $itemUUID) {
                    $resultingItems[$itemUUID] = $this->items[$this->getIndexKey()][$itemUUID] ?? null;
                }
            }
        }

        $resultingItems = array_values(
            array_slice(
                array_filter($resultingItems),
                $query->getFrom(),
                $query->getSize()
            )
        );

        $result = new Result($query, count($this->items[$this->getIndexKey()]), count($resultingItems));
        foreach ($resultingItems as $resultingItem) {
            $result->addItem($resultingItem);
        }

        return $result;
    }

    /**
     * Get items.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Create an index.
     *
     * @param ImmutableConfig $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(ImmutableConfig $config)
    {
        if (array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceExistsException::indexExists();
        }

        $this->items[$this->getIndexKey()] = [];
    }

    /**
     * Delete an index.
     */
    public function deleteIndex()
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        unset($this->items[$this->getIndexKey()]);
    }

    /**
     * Reset the index.
     */
    public function resetIndex()
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $this->items[$this->getIndexKey()] = [];
    }

    /**
     * Checks the index.
     *
     * @return bool
     */
    public function checkIndex(): bool
    {
        return isset($this->items[$this->getIndexKey()]);
    }

    /**
     * Generate item document.
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        parent::addItem($item);
    }

    /**
     * Generate item documents.
     *
     * @param Item[] $items
     */
    public function addItems(array $items)
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        parent::addItems($items);
    }

    /**
     * Delete item document by uuid.
     *
     * @param ItemUUID $uuid
     */
    public function deleteItem(ItemUUID $uuid)
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        parent::deleteItem($uuid);
    }

    /**
     * Delete item documents by uuid.
     *
     * @param ItemUUID[] $uuids
     */
    public function deleteItems(array $uuids)
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        parent::deleteItems($uuids);
    }

    /**
     * Flush all.
     *
     * This flush can be avoided if not enough items have been generated by
     * setting $skipIfLess = true
     *
     * @param int  $bulkNumber
     * @param bool $skipIfLess
     *
     * @throws ResourceNotAvailableException
     */
    public function flush(
        int $bulkNumber = 500,
        bool $skipIfLess = false
    ) {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        parent::flush();
    }

    /**
     * Flush items.
     *
     * @param Item[]     $itemsToUpdate
     * @param ItemUUID[] $itemsToDelete
     */
    protected function flushItems(
        array $itemsToUpdate,
        array $itemsToDelete
    ) {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        foreach ($itemsToUpdate as $itemToUpdate) {
            $this->items[$this->getIndexKey()][$itemToUpdate->getUUID()->composeUUID()] = $itemToUpdate;
        }

        foreach ($itemsToDelete as $itemToDelete) {
            unset($this->items[$this->getIndexKey()][$itemToDelete->composeUUID()]);
        }
    }

    /**
     * Config the index.
     *
     * @param Config $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(Config $config)
    {
        throw new LogicException('Config logic cannot be tested against memory implementation, but only in final implementation');
    }

    /**
     * Get index position by credentials.
     *
     * @return string
     */
    private function getIndexKey(): string
    {
        return $this
            ->getRepositoryReference()
            ->compose();
    }
}
