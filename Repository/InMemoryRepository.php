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

use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
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
            throw ResourceNotAvailableException::indexNotAvailable();
        }

        $this->normalizeItemsArray();
        $resultingItems = [];

        if (!empty($query->getFilters())) {
            foreach ($query->getFilters() as $filter) {
                if ('_id' !== $filter->getField()) {
                    throw new LogicException('Queries by field different than UUID not allowed in InMemoryRepository. Only for testing purposes.');
                }

                $itemUUIDs = $filter->getValues();
                foreach ($itemUUIDs as $itemUUID) {
                    $resultingItems[$itemUUID] = $this->items[$this->getIndexKey()][$itemUUID] ?? null;
                }
            }
        } else {
            $resultingItems = $this->items[$this->getIndexKey()];
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
     * Create an index.
     *
     * @param null|string $language
     *
     * @throws ResourceExistsException
     */
    public function createIndex(? string $language)
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
            throw ResourceNotAvailableException::indexNotAvailable();
        }

        unset($this->items[$this->getIndexKey()]);
    }

    /**
     * Reset the index.
     */
    public function resetIndex()
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            throw ResourceNotAvailableException::indexNotAvailable();
        }

        $this->items[$this->getIndexKey()] = [];
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
        $this->normalizeItemsArray();
        foreach ($itemsToUpdate as $itemToUpdate) {
            $this->items[$this->getIndexKey()][$itemToUpdate->getUUID()->composeUUID()] = $itemToUpdate;
        }

        foreach ($itemsToDelete as $itemToDelete) {
            unset($this->items[$this->getIndexKey()][$itemToDelete->composeUUID()]);
        }
    }

    /**
     * Normalize items array.
     */
    private function normalizeItemsArray()
    {
        if (!array_key_exists($this->getIndexKey(), $this->items)) {
            $this->items[$this->getIndexKey()] = [];
        }
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
