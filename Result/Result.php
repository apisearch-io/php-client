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

use Puntmig\Search\Model\HttpTransportable;
use Puntmig\Search\Model\Item;
use Puntmig\Search\Query\Query;

/**
 * Class Result.
 */
class Result implements HttpTransportable
{
    /**
     * @var Query
     *
     * Query associated
     */
    private $query;

    /**
     * @var Item[]
     *
     * Items
     */
    private $items = [];

    /**
     * @var array
     *
     * Suggests
     */
    private $suggests = [];

    /**
     * @var null|Aggregations
     *
     * Aggregations
     */
    private $aggregations;

    /**
     * Total items.
     *
     * @var int
     */
    private $totalItems;

    /**
     * Total hits.
     *
     * @var int
     */
    private $totalHits;

    /**
     * Items grouped by types cache.
     *
     * @var array
     */
    private $itemsGroupedByTypeCache;

    /**
     * Result constructor.
     *
     * @param Query $query
     * @param int   $totalItems
     * @param int   $totalHits
     */
    public function __construct(
        Query $query,
        int $totalItems,
        int $totalHits
    ) {
        $this->query = $query;
        $this->totalItems = $totalItems;
        $this->totalHits = $totalHits;
    }

    /**
     * Create by.
     *
     * @param Query             $query
     * @param int               $totalItems
     * @param int               $totalHits
     * @param Aggregations|null $aggregations
     * @param string[]          $suggests
     * @param Item[]            $items
     *
     * @return Result
     */
    public static function create(
        Query $query,
        int $totalItems,
        int $totalHits,
        ? Aggregations $aggregations,
        array $suggests,
        array $items
    ) : Result {
        $result = new self(
            $query,
            $totalItems,
            $totalHits
        );

        $result->aggregations = $aggregations;
        $result->suggests = $suggests;
        $result->items = $items;

        return $result;
    }

    /**
     * Add product.
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    /**
     * Get items.
     *
     * @return Item[]
     */
    public function getItems() : array
    {
        return $this->items;
    }

    /**
     * Get items grouped by type.
     *
     * @return array
     */
    public function getItemsGroupedByTypes() : array
    {
        if (is_array($this->itemsGroupedByTypeCache)) {
            return $this->itemsGroupedByTypeCache;
        }

        $items = $this->getItems();
        $itemsGroupedByType = [];
        foreach ($items as $item) {
            if (!isset($itemsGroupedByType[$item->getType()])) {
                $itemsGroupedByType[$item->getType()] = [];
            }
            $itemsGroupedByType[$item->getType()][] = $item;
        }

        $this->itemsGroupedByTypeCache = $itemsGroupedByType;

        return $itemsGroupedByType;
    }

    /**
     * Get Items by a certain type.
     *
     * @param string $type
     *
     * @return Item[]
     */
    public function getItemsByType(string $type) : array
    {
        return $this->getItemsGroupedByTypes()[$type] ?? [];
    }

    /**
     * Get Items by a certain types.
     *
     * @param array $types
     *
     * @return Item[]
     */
    public function getItemsByTypes(array $types) : array
    {
        return array_filter(
            $this->getItems(),
            function (Item $item) use ($types) {
                return in_array(
                    $item->getType(),
                    $types
                );
            }
        );
    }

    /**
     * Get first result.
     *
     * @return null|Item
     */
    public function getFirstItem()
    {
        $results = $this->getItems();

        if (empty($results)) {
            return null;
        }

        $firstItem = reset($results);

        return $firstItem;
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
    public function getAggregation(string $name) : ? Aggregation
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
    public function hasNotEmptyAggregation(string $name) : bool
    {
        if (is_null($this->aggregations)) {
            return false;
        }

        return $this
            ->aggregations
            ->hasNotEmptyAggregation($name);
    }

    /**
     * Get metadata aggregation.
     *
     * @param string $field
     *
     * @return null|Aggregation
     */
    public function getMetaAggregation(string $field) : ? Aggregation
    {
        if (is_null($this->aggregations)) {
            return null;
        }

        return $this
            ->aggregations
            ->getMetaAggregation($field);
    }

    /**
     * Add suggest.
     *
     * @param string $suggest
     */
    public function addSuggest(string $suggest)
    {
        $this->suggests[$suggest] = $suggest;
    }

    /**
     * Get suggests.
     *
     * @return string[]
     */
    public function getSuggests() : array
    {
        return array_values($this->suggests);
    }

    /**
     * Get query.
     *
     * @return Query
     */
    public function getQuery() : Query
    {
        return $this->query;
    }

    /**
     * Total items.
     *
     * @return int
     */
    public function getTotalItems() : int
    {
        return $this->totalItems;
    }

    /**
     * Get total hits.
     *
     * @return int
     */
    public function getTotalHits() : int
    {
        return $this->totalHits;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return array_filter([
            'query' => $this->query->toArray(),
            'total_items' => $this->totalItems,
            'total_hits' => $this->totalHits,
            'items' => array_map(function (Item $item) {
                return $item->toArray();
            }, $this->items),
            'aggregations' => $this->aggregations instanceof Aggregations
                ? $this->aggregations->toArray()
                : null,
            'suggests' => $this->suggests,
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
     * @return Result
     */
    public static function createFromArray(array $array) : Result
    {
        return self::create(
            Query::createFromArray($array['query']),
            $array['total_items'] ?? 0,
            $array['total_hits'] ?? 0,
            isset($array['aggregations'])
                ? Aggregations::createFromArray($array['aggregations'])
                : null,
            $array['suggests'] ?? [],
            array_map(function (array $item) {
                return Item::createFromArray($item);
            }, $array['items'] ?? [])
        );
    }
}
