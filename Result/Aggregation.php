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

namespace Puntmig\Search\Result;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

use Puntmig\Search\Model\HttpTransportable;
use Puntmig\Search\Query\Filter;

/**
 * Class Aggregation.
 */
class Aggregation implements IteratorAggregate, HttpTransportable
{
    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var Counter[]
     *
     * Counters
     */
    private $counters = [];

    /**
     * @var int
     *
     * Application type
     */
    private $applicationType;

    /**
     * @var int
     *
     * Total elements
     */
    private $totalElements;

    /**
     * @var array
     *
     * Active elements
     */
    private $activeElements;

    /**
     * @var int
     *
     * Lowest level
     */
    private $lowestLevel;

    /**
     * Aggregation constructor.
     *
     * @param string $name
     * @param int    $applicationType
     * @param int    $totalElements
     * @param array  $activeElements
     */
    public function __construct(
        string $name,
        int $applicationType,
        int $totalElements,
        array $activeElements
    ) {
        $this->name = $name;
        $this->applicationType = $applicationType;
        $this->totalElements = $totalElements;
        $this->activeElements = array_combine(
            array_values($activeElements),
            array_values($activeElements)
        );
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
        if ($counter == 0) {
            return;
        }

        $counter = Counter::createByActiveElements(
            $name,
            $counter,
            $this->activeElements
        );

        if (!$counter instanceof Counter) {
            return;
        }

        /**
         * The entry is used.
         * This block should take in account when the filter is of type
         * levels, but only levels.
         */
        if (
            $this->applicationType & Filter::MUST_ALL_WITH_LEVELS &&
            $this->applicationType & ~Filter::MUST_ALL &&
            $counter->isUsed()
        ) {
            $this->activeElements[$counter->getId()] = $counter;

            return;
        }

        $this->counters[$counter->getId()] = $counter;
        $this->lowestLevel = is_null($this->lowestLevel)
            ? $counter->getLevel()
            : min($this->lowestLevel, $counter->getLevel());
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
        return (bool) (($this->applicationType & Filter::MUST_ALL) === true);
    }

    /**
     * Aggregation has levels.
     *
     * @return bool
     */
    public function hasLevels() : bool
    {
        return (bool) ($this->applicationType & Filter::MUST_ALL_WITH_LEVELS);
    }

    /**
     * Get counter.
     *
     * @param string $name
     *
     * @return null|Counter
     */
    public function getCounter(string $name) : ? Counter
    {
        return $this->counters[$name] ?? null;
    }

    /**
     * Get all elements.
     *
     * @return Counter[]
     */
    public function getAllElements() : array
    {
        return $this->counters + $this->activeElements;
    }

    /**
     * Get total elements.
     *
     * @return int
     */
    public function getTotalElements() : int
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

        if ($this->applicationType & FILTER::MUST_ALL_WITH_LEVELS) {
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
     * Sort by value.
     */
    public function sortByName()
    {
        uasort($this->counters, function (Counter $a, Counter $b) {
            return $a->getName() > $b->getName();
        });
    }

    /**
     * Clean results by level and remove all levels higher than the lowest.
     */
    public function cleanCountersByLevel()
    {
        foreach ($this->counters as $pos => $counter) {
            if ($counter->getLevel() !== $this->lowestLevel) {
                if ($counter->isUsed()) {
                    $this->activeElements[$counter->getId()] = $counter;
                }
                unset($this->counters[$pos]);
            }
        }
    }

    /**
     * Retrieve an external iterator.
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
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
    public function isEmpty() : bool
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
        return [
            'name' => $this->name,
            'counters' => array_map(function (Counter $counter) {
                return $counter->toArray();
            }, $this->counters),
            'application_type' => $this->applicationType,
            'total_elements' => $this->totalElements,
            'active_elements' => array_map(function ($counter) {
                return ($counter instanceof Counter)
                    ? $counter->toArray()
                    : $counter;
            }, $this->activeElements),
            'lowest_level' => $this->lowestLevel,
        ];
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
        foreach ($array['active_elements'] as $activeElementName => $activeElement) {
            $activeElements[$activeElementName] = is_array($activeElement)
                ? Counter::createFromArray($activeElement)
                : $activeElement;
        }

        $aggregation = new self(
            $array['name'],
            $array['application_type'],
            $array['total_elements'],
            []
        );
        $aggregation->activeElements = $activeElements;
        $aggregation->counters = array_map(function (array $counter) {
            return Counter::createFromArray($counter);
        }, $array['counters']);

        $aggregation->lowestLevel = $array['lowest_level'];

        return $aggregation;
    }
}
