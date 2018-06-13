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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Result\Events;
use Exception;

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
     * Query over events.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Events
     *
     * @throws Exception
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
