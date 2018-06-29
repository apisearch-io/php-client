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

namespace Apisearch\Result;

use Apisearch\Log\Log;
use Apisearch\Model\HttpTransportable;
use Apisearch\Query\Query;

/**
 * Class Logs.
 */
class Logs implements HttpTransportable
{
    /**
     * @var Query
     *
     * Query associated
     */
    private $query;

    /**
     * @var Log[]
     *
     * Logs
     */
    private $logs = [];

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
     * @param Log[]             $logs
     *
     * @return Logs
     */
    public static function create(
        Query $query,
        int $totalHits,
        ? Aggregations $aggregations,
        array $logs
    ): self {
        $result = new self(
            $query,
            $totalHits
        );

        $result->aggregations = $aggregations;
        $result->logs = $logs;

        return $result;
    }

    /**
     * Add log.
     *
     * @param Log $log
     */
    public function addLog(Log $log)
    {
        $this->logs[] = $log;
    }

    /**
     * Get logs.
     *
     * @return Log[]
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Get first result.
     *
     * @return null|Log
     */
    public function getFirstLog()
    {
        $results = $this->getLogs();

        if (empty($results)) {
            return null;
        }

        $firstLog = reset($results);

        return $firstLog;
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
            'logs' => array_map(function (Log $log) {
                return $log->toArray();
            }, $this->logs),
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
     * @return Logs
     */
    public static function createFromArray(array $array): self
    {
        return self::create(
            Query::createFromArray($array['query']),
            $array['total_hits'] ?? 0,
            isset($array['aggregations'])
                ? Aggregations::createFromArray($array['aggregations'])
                : null,
            array_map(function (array $log) {
                return Log::createFromArray($log);
            }, $array['logs'] ?? [])
        );
    }
}
