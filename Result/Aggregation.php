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

use Apisearch\Model\HttpTransportable;
use Apisearch\Query\Filter;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class Aggregation.
 */
class Aggregation implements IteratorAggregate, HttpTransportable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Counter[]
     */
    private $counters = [];

    /**
     * @var int
     */
    private $applicationType;

    /**
     * @var int
     */
    private $totalElements;

    /**
     * @var array
     */
    private $activeElements;

    /**
     * @var int
     */
    private $highestActiveLevel = 0;

    /**
     * @var array
     */
    private $metadata;

    /**
     * Aggregation constructor.
     *
     * @param string $name
     * @param int    $applicationType
     * @param int    $totalElements
     * @param array  $activeElements
     * @param array  $metadata
     */
    public function __construct(
        string $name,
        int $applicationType,
        int $totalElements,
        array $activeElements,
        array $metadata = []
    ) {
        $this->name = $name;
        $this->applicationType = $applicationType;
        $this->totalElements = $totalElements;
        $this->activeElements = array_combine(
            array_values($activeElements),
            array_values($activeElements)
        );
        $this->metadata = $metadata;
    }

    /**
     * Add aggregation counter.
     *
     * @param string $name
     * @param int    $counter
     */
    public function addCounter(
        string $name,
        int $counter
    ) {
        if (0 == $counter) {
            return;
        }

        $counterInstance = Counter::createByActiveElements(
            $name,
            $counter,
            $this->activeElements
        );

        if (!$counterInstance instanceof Counter) {
            return;
        }

        /*
         * The entry is used.
         * This block should take in account when the filter is of type
         * levels, but only levels.
         */
        if (
            $this->applicationType & Filter::MUST_ALL_WITH_LEVELS &&
            $this->applicationType & ~Filter::MUST_ALL &&
            $counterInstance->isUsed()
        ) {
            $this->activeElements[$counterInstance->getId()] = $counterInstance;
            $this->highestActiveLevel = max(
                $counterInstance->getLevel(),
                $this->highestActiveLevel
            );

            return;
        }

        $this->counters[$counterInstance->getId()] = $counterInstance;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get counters.
     *
     * @return Counter[]
     */
    public function getCounters(): array
    {
        return $this->counters;
    }

    /**
     * Return if the aggregation belongs to a filter.
     *
     * @return bool
     */
    public function isFilter(): bool
    {
        return ($this->applicationType & Filter::MUST_ALL) > 0;
    }

    /**
     * Aggregation has levels.
     *
     * @return bool
     */
    public function hasLevels(): bool
    {
        return ($this->applicationType & Filter::MUST_ALL_WITH_LEVELS) > 0;
    }

    /**
     * Get counter.
     *
     * @param string $name
     *
     * @return Counter|null
     */
    public function getCounter(string $name): ? Counter
    {
        return $this->counters[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     *
     * @return void
     */
    public function withMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get all elements.
     *
     * @return Counter[]
     */
    public function getAllElements(): array
    {
        return $this->counters + $this->activeElements;
    }

    /**
     * Get total elements.
     *
     * @return int
     */
    public function getTotalElements(): int
    {
        return $this->totalElements;
    }

    /**
     * Get active elements.
     *
     * @return array
     */
    public function getActiveElements(): array
    {
        if (empty($this->activeElements)) {
            return [];
        }

        if (Filter::MUST_ALL_WITH_LEVELS === $this->applicationType) {
            $value = [array_reduce(
                $this->activeElements,
                function ($carry, $counter) {
                    if (!$counter instanceof Counter) {
                        return $carry;
                    }

                    if (!$carry instanceof Counter) {
                        return $counter;
                    }

                    return $carry->getLevel() > $counter->getLevel()
                        ? $carry
                        : $counter;
                }, null)];

            return is_null($value)
                ? []
                : $value;
        }

        return $this->activeElements;
    }

    /**
     * Clean results by level and remove all levels higher than the lowest.
     */
    public function cleanCountersByLevel()
    {
        foreach ($this->counters as $pos => $counter) {
            if ($counter->getLevel() !== $this->highestActiveLevel + 1) {
                unset($this->counters[$pos]);
            }
        }
    }

    /**
     * Retrieve an external iterator.
     *
     * @see  http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *                     <b>Traversable</b>
     *
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->counters);
    }

    /**
     * Aggregation is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return
            empty($this->activeElements) &&
            empty($this->counters);
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'counters' => array_values(array_map(function (Counter $counter) {
                return $counter->toArray();
            }, $this->counters)),
            'application_type' => Filter::AT_LEAST_ONE === $this->applicationType
                ? null
                : $this->applicationType,
            'total_elements' => 0 === $this->totalElements
                ? null
                : $this->totalElements,
            'active_elements' => array_values(array_map(function ($counter) {
                return ($counter instanceof Counter)
                    ? $counter->toArray()
                    : $counter;
            }, $this->activeElements)),
            'highest_active_level' => 0 === $this->highestActiveLevel
                ? null
                : $this->highestActiveLevel,
            'metadata' => $this->metadata,
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
     * @return self
     */
    public static function createFromArray(array $array): self
    {
        $activeElements = [];
        foreach (($array['active_elements'] ?? []) as $activeElement) {
            $activeElements[] = is_array($activeElement)
                ? Counter::createFromArray($activeElement)
                : $activeElement;
        }

        $aggregation = new self(
            $array['name'],
            (int) ($array['application_type'] ?? Filter::AT_LEAST_ONE),
            (int) ($array['total_elements'] ?? 0),
            []
        );
        $aggregation->activeElements = $activeElements;
        $counters = array_map(function (array $counter) {
            return Counter::createFromArray($counter);
        }, $array['counters'] ?? []);
        foreach ($counters as $counter) {
            $aggregation->counters[$counter->getId()] = $counter;
        }

        $aggregation->highestActiveLevel = $array['highest_active_level'] ?? 0;
        $aggregation->metadata = $array['metadata'] ?? [];

        return $aggregation;
    }
}
