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

namespace Puntmig\Search\Event;

use Puntmig\Search\Repository\RepositoryWithCredentials;

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
     * Create repository.
     *
     * @param bool $removeIfExists
     */
    public function createRepository(bool $removeIfExists = false)
    {
        if ($removeIfExists) {
            unset($this->events[$this->getAppId()]);
        }

        if (!isset($this->events[$this->getAppId()])) {
            $this->events[$this->getAppId()] = [];
        }
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
     */
    public function all(
        string $name = null,
        ? int $from = null,
        ? int $to = null,
        ? int $length = 10,
        ? int $offset = 0
    ): array {
        if (!isset($this->events[$this->getAppId()])) {
            return [];
        }

        return array_slice(
            array_filter(
                $this->events[$this->getAppId()],
                function (Event $event) use ($from, $to, $name) {
                    return
                        (is_null($this->getAppId()) || ($this->getAppId() === $event->getAppId())) &&
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
     */
    public function save(Event $event)
    {
        if (!isset($this->events[$this->getAppId()])) {
            $this->events[$this->getAppId()] = [];
        }

        $this->events[$this->getAppId()][] = $event;
    }

    /**
     * Get last event.
     *
     * @return Event|null
     */
    public function last(): ? Event
    {
        if (!isset($this->events[$this->getAppId()])) {
            return null;
        }

        $lastEvent = end($this->events[$this->getAppId()]);

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
     */
    public function stats(
        ? int $from = null,
        ? int $to = null
    ): Stats {
    }
}
