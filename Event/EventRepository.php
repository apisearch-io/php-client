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

namespace Apisearch\Event;

use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Repository\WithRepositoryReference;

/**
 * Class EventRepository.
 */
interface EventRepository extends WithRepositoryReference
{
    /**
     * Create index.
     *
     * @throws ResourceExistsException
     */
    public function createIndex();

    /**
     * Delete index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex();

    /**
     * Save event.
     *
     * @param Event $event
     *
     * @throws ResourceNotAvailableException
     */
    public function save(Event $event);

    /**
     * Get all events.
     *
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
     * @param string|null $sortBy
     *
     * @return Event[]
     *
     * @throws ResourceNotAvailableException
     */
    public function all(
        string $name = null,
        ? int $from = null,
        ? int $to = null,
        ? int $length = 10,
        ? int $offset = 0,
        ? string $sortBy = SortBy::OCCURRED_ON_DESC
    ): array;

    /**
     * Get last event.
     *
     * @return Event|null
     *
     * @throws ResourceNotAvailableException
     */
    public function last(): ? Event;

    /**
     * Get stats.
     *
     * @param int|null $from
     * @param int|null $to
     *
     * @return Stats
     *
     * @throws ResourceNotAvailableException
     */
    public function stats(
        ? int $from = null,
        ? int $to = null
    ): Stats;
}
