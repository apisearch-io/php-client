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

namespace Apisearch\Result;

use Apisearch\Event\Event;
use Apisearch\Model\HttpTransportable;
use Apisearch\Query\Query;

/**
 * Class Events.
 */
class Events implements HttpTransportable
{
    /**
     * @var Query
     *
     * Query associated
     */
    private $query;

    /**
     * @var Event[]
     *
     * Events
     */
    private $events = [];

    /**
     * @var null|Aggregations
     *
     * Aggregations
     */
    private $aggregations;

    /**
     * Total hits.
     *
     * @var int
     */
    private $totalHits;

    /**
     * Result constructor.
     *
     * @param Query $query
     * @param int   $totalHits
     */
    public function __construct(
        Query $query,
        int $totalHits
    ) {
        $this->query = $query;
        $this->totalHits = $totalHits;
    }

    /**
     * Create by.
     *
     * @param Query             $query
     * @param int               $totalHits
     * @param Aggregations|null $aggregations
     * @param Event[]           $events
     *
     * @return Events
     */
    public static function create(
        Query $query,
        int $totalHits,
        ? Aggregations $aggregations,
        array $events
    ): self {
        $result = new self(
            $query,
            $totalHits
        );

        $result->aggregations = $aggregations;
        $result->events = $events;

        return $result;
    }

    /**
     * Add event.
     *
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * Get events.
     *
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Get first result.
     *
     * @return null|Event
     */
    public function getFirstEvent()
    {
        $results = $this->getEvents();

        if (empty($results)) {
            return null;
        }

        $firstEvent = reset($results);

        return $firstEvent;
    }

    /**
     * Set aggregations.
     *
     * @param Aggregations $aggregations
     */
    public function setAggregations(Aggregations $aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get aggregations.
     *
     * @return null|Aggregations
     */
    public function getAggregations(): ? Aggregations
    {
        return $this->aggregations;
    }

    /**
     * Get aggregation.
     *
     * @param string $name
     *
     * @return null|Aggregation
     */
    public function getAggregation(string $name): ? Aggregation
    {
        if (is_null($this->aggregations)) {
            return null;
        }

        return $this
            ->aggregations
            ->getAggregation($name);
    }

    /**
     * Has not empty aggregation.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasNotEmptyAggregation(string $name): bool
    {
        if (is_null($this->aggregations)) {
            return false;
        }

        return $this
            ->aggregations
            ->hasNotEmptyAggregation($name);
    }

    /**
     * Get query.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * Get total hits.
     *
     * @return int
     */
    public function getTotalHits(): int
    {
        return $this->totalHits;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query->toArray(),
            'total_hits' => $this->totalHits,
            'events' => array_map(function (Event $event) {
                return $event->toArray();
            }, $this->events),
            'aggregations' => $this->aggregations instanceof Aggregations
                ? $this->aggregations->toArray()
                : null,
        ], function ($element) {
            return
            !(
                is_null($element) ||
                (is_array($element) && empty($element))
            );
        });
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return Events
     */
    public static function createFromArray(array $array): self
    {
        return self::create(
            Query::createFromArray($array['query']),
            $array['total_hits'] ?? 0,
            isset($array['aggregations'])
                ? Aggregations::createFromArray($array['aggregations'])
                : null,
            array_map(function (array $event) {
                return Event::createFromArray($event);
            }, $array['events'] ?? [])
        );
    }
}
