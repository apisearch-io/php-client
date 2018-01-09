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
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Result\Events;

/**
 * Class InMemoryEventRepository.
 */
class InMemoryEventRepository extends RepositoryWithCredentials implements EventRepository
{
    /**
     * @var array
     *
     * Events
     */
    private $events = [];

    /**
     * Create index.
     *
     * @throws ResourceExistsException
     */
    public function createIndex()
    {
        if (array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceExistsException::eventsIndexExists();
        }

        $this->events[$this->getIndexKey()] = [];
    }

    /**
     * Delete index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable('Index not found in InMemoryEventRepository');
        }

        unset($this->events[$this->getIndexKey()]);
    }

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
    ): Events {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable('Index not found in InMemoryEventRepository');
        }

        throw new \Exception('Endpoint not implemented');
    }

    /**
     * Save event.
     *
     * @param Event $event
     *
     * @throws ResourceNotAvailableException
     */
    public function save(Event $event)
    {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable('Index not found in InMemoryEventRepository');
        }

        $this->events[$this->getIndexKey()][] = $event;
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
