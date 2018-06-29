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
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class Aggregations.
 */
class Aggregations implements IteratorAggregate, HttpTransportable
{
    /**
     * @var Aggregation[]
     *
     * Aggregations
     */
    private $aggregations = [];

    /**
     * @var int
     *
     * Total elements
     */
    private $totalElements;

    /**
     * Aggregations constructor.
     *
     * @param int $totalElements
     */
    public function __construct(int $totalElements)
    {
        $this->totalElements = $totalElements;
    }

    /**
     * Add aggregation value.
     *
     * @param string      $name
     * @param Aggregation $aggregation
     */
    public function addAggregation(
        string $name,
        Aggregation $aggregation
    ) {
        $this->aggregations[$name] = $aggregation;
    }

    /**
     * Get aggregations.
     *
     * @return Aggregation[]
     */
    public function getAggregations(): array
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
        return $this->aggregations[$name] ?? null;
    }

    /**
     * Return if the needed aggregation exists and if is not empty.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasNotEmptyAggregation(string $name): bool
    {
        return
            !is_null($this->getAggregation($name)) &&
            !$this
                ->getAggregation($name)
                ->isEmpty();
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
        return new ArrayIterator($this->aggregations);
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'aggregations' => array_map(function (Aggregation $aggregation) {
                return $aggregation->toArray();
            }, $this->getAggregations()),
            'total_elements' => $this->getTotalElements(),
        ]);
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
        $aggregations = new self(
            $array['total_elements'] ?? 0
        );

        if (isset($array['aggregations'])) {
            foreach ($array['aggregations'] as $aggregationName => $aggregation) {
                $aggregations->addAggregation(
                    $aggregationName,
                    Aggregation::createFromArray($aggregation)
                );
            }
        }

        return $aggregations;
    }
}
