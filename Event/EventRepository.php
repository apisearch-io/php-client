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
use Apisearch\Query\Query;
use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Result\Events;

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
     * Query over events.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Events
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Events;

    /**
     * Get last event.
     *
     * @return Event|null
     *
     * @throws ResourceNotAvailableException
     */
    public function last(): ? Event;
}
