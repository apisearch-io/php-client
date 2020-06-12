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

use Apisearch\Exception\MockException;
use Apisearch\Model\Changes;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Class MockRepository.
 */
class MockRepository extends Repository
{
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
        $this->throwMockException();
    }

    /**
     * Search across the index types.
     *
     * @param Query $query
     * @param array $parameters
     *
     * @return Result
     *
     * @throws MockException
     */
    public function query(
        Query $query,
        array $parameters = []
    ): Result {
        $this->throwMockException();
    }

    /**
     * Update items.
     *
     * @param Query   $query
     * @param Changes $changes
     *
     * @throws MockException
     */
    public function updateItems(
        Query $query,
        Changes $changes
    ) {
        $this->throwMockException();
    }

    /**
     * Delete items by query.
     *
     * @param Query $query
     */
    public function deleteItemsByQuery(Query $query)
    {
        $this->throwMockException();
    }

    /**
     * Throw exception.
     *
     * @throws MockException
     */
    private function throwMockException()
    {
        throw MockException::isAMock();
    }
}
