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

use Puntmig\Search\Exception\EventException;

/**
 * Class InMemoryEventRepository.
 */
class InMemoryEventRepository implements EventRepository
{
    /**
     * @var Event[]
     *
     * Events
     */
    private $events = [];

    /**
     * Get all events.
     *
     * @param string|null $key
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
     *
     * @return Event[]
     */
    public function all(
        string $key = null,
        string $name = null,
        ? int $from = null,
        ? int $to = null,
        ? int $length = 10,
        ? int $offset = 0
    ) : array {
        return array_slice(
            array_filter(
                $this->events,
                function (Event $event) use ($key, $from, $to, $name) {
                    return
                        (is_null($key) || ($key === $event->getKey())) &&
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
     * @throws EventException
     */
    public function save(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * Get last event.
     *
     * @return Event|null
     */
    public function last() : ? Event
    {
        $lastEvent = end($this->events);

        return ($lastEvent instanceof Event)
            ? $lastEvent
            : null;
    }
}
