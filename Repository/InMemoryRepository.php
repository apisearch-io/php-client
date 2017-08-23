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

namespace Puntmig\Search\Repository;

use LogicException;

use Puntmig\Search\Model\Item;
use Puntmig\Search\Model\ItemUUID;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

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
     */
    public function query(Query $query): Result
    {
        $this->normalizeItemsArray();
        $resultingItems = [];

        if (!empty($query->getFilters())) {
            foreach ($query->getFilters() as $filter) {
                if ($filter->getField() !== '_id') {
                    throw new LogicException('Queries by field different than UUID not allowed in InMemoryRepository. Only for testing purposes.');
                }

                $itemUUIDs = $filter->getValues();
                foreach ($itemUUIDs as $itemUUID) {
                    $resultingItems[$itemUUID] = $this->items[$this->getKey()][$itemUUID] ?? null;
                }
            }
        } else {
            $resultingItems = $this->items[$this->getKey()];
        }

        $resultingItems = array_values(
            array_slice(
                array_filter($resultingItems),
                $query->getFrom(),
                $query->getSize()
            )
        );

        $result = new Result($query, count($this->items[$this->getKey()]), count($resultingItems));
        foreach ($resultingItems as $resultingItem) {
            $result->addItem($resultingItem);
        }

        return $result;
    }

    /**
     * Reset the index.
     *
     * @var null|string
     */
    public function reset(? string $language)
    {
        $this->items[$this->getKey()] = [];
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
            $this->items[$this->getKey()][$itemToUpdate->getUUID()->composeUUID()] = $itemToUpdate;
        }

        foreach ($itemsToDelete as $itemToDelete) {
            unset($this->items[$this->getKey()][$itemToDelete->composeUUID()]);
        }
    }

    /**
     * Normalize items array.
     */
    private function normalizeItemsArray()
    {
        if (!array_key_exists($this->getKey(), $this->items)) {
            $this->items[$this->getKey()] = [];
        }
    }
}
