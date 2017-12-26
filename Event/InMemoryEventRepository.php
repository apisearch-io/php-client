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
use Apisearch\Repository\RepositoryWithCredentials;

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
            throw ResourceNotAvailableException::eventsIndexNotAvailable();
        }

        unset($this->events[$this->getIndexKey()]);
    }

    /**
     * Get all events.
     *
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
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
        ? int $offset = 0
    ): array {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable();
        }

        return array_slice(
            array_filter(
                $this->events[$this->getIndexKey()],
                function (Event $event) use ($from, $to, $name) {
                    return
                        (is_null($name) || ($name === $event->getName())) &&
                        (is_null($from) || ($event->getOccurredOn() >= $from)) &&
                        (is_null($to) || ($event->getOccurredOn() < $to));
                }
            ),
            $offset,
            $length
        );
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
            throw ResourceNotAvailableException::eventsIndexNotAvailable();
        }

        $this->events[$this->getIndexKey()][] = $event;
    }

    /**
     * Get last event.
     *
     * @return Event|null
     *
     * @throws ResourceNotAvailableException
     */
    public function last(): ? Event
    {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable();
        }

        $lastEvent = end($this->events[$this->getIndexKey()]);

        return ($lastEvent instanceof Event)
            ? $lastEvent
            : null;
    }

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
    ): Stats {
        if (!array_key_exists($this->getIndexKey(), $this->events)) {
            throw ResourceNotAvailableException::eventsIndexNotAvailable();
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
