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

namespace Apisearch\Repository;

use Apisearch\Model\Changes;
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
    protected $items = [];

    /**
     * Search across the index types.
     *
     * @param Query $query
     * @param array $parameters
     *
     * @return Result
     */
    public function query(
        Query $query,
        array $parameters = []
    ): Result {
        $resultingItems = $this->items[$this->getIndexKey()] ?? [];

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

        $items = $this->items[$this->getIndexKey()] ?? [];
        $result = new Result($query->getUUID(), count($items), count($resultingItems));
        foreach ($resultingItems as $resultingItem) {
            $result->addItem($resultingItem);
        }

        return $result;
    }

    /**
     * Update items.
     *
     * @param Query   $query
     * @param Changes $changes
     */
    public function updateItems(
        Query $query,
        Changes $changes
    ) {
        throw new LogicException('Update endpoint cannot be tested against memory implementation, but only in final implementation');
    }

    /**
     * Delete items by query.
     *
     * @param Query $query
     */
    public function deleteItemsByQuery(Query $query)
    {
        throw new LogicException('Update endpoint cannot be tested against memory implementation, but only in final implementation');
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
     * Flush items.
     *
     * @param Item[]     $itemsToUpdate
     * @param ItemUUID[] $itemsToDelete
     */
    protected function flushItems(
        array $itemsToUpdate,
        array $itemsToDelete
    ) {
        foreach ($itemsToUpdate as $itemToUpdate) {
            $this->items[$this->getIndexKey()][$itemToUpdate->getUUID()->composeUUID()] = $itemToUpdate;
        }

        foreach ($itemsToDelete as $itemToDelete) {
            unset($this->items[$this->getIndexKey()][$itemToDelete->composeUUID()]);
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
