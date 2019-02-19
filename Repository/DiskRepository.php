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

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Class DiskRepository.
 */
class DiskRepository extends InMemoryRepository
{
    /**
     * @var string
     *
     * File name
     */
    private $filename;

    /**
     * DiskAppRepository constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        parent::__construct();
        $this->filename = $filename;
    }

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
        $this->load();

        return parent::query($query);
    }

    /**
     * Get items.
     *
     * @return array
     */
    public function getItems(): array
    {
        $this->load();

        return parent::getItems();
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
        $this->load();
        parent::flushItems(
            $itemsToUpdate,
            $itemsToDelete
        );
        $this->save();
    }

    /**
     * Load.
     */
    private function load()
    {
        if (!file_exists($this->filename)) {
            return;
        }

        $this->items = unserialize(
            file_get_contents(
                $this->filename
            )
        ) ?? [];
    }

    /**
     * Save.
     */
    private function save()
    {
        file_put_contents(
            $this->filename,
            serialize($this->items)
        );
        $this->items = [];
    }
}
