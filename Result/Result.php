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
use Apisearch\Model\Item;
use Apisearch\Query\Query;

/**
 * Class Result.
 */
class Result implements HttpTransportable
{
    /**
     * @var string
     */
    private $queryUUID;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var Item[]
     */
    private $items = [];

    /**
     * @var string|null
     */
    private $autocomplete = null;

    /**
     * @var array
     */
    private $suggestions = [];

    /**
     * @var Aggregations|null
     */
    private $aggregations;

    /**
     * @var int
     */
    private $totalItems;

    /**
     * @var int
     */
    private $totalHits;

    /**
     * @var array
     */
    private $itemsGroupedByTypeCache;

    /**
     * @var Result[]
     */
    private $subresults = [];

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var array
     */
    private $context = [];

    /**
     * Result constructor.
     *
     * @param Query|null $query
     * @param int        $totalItems
     * @param int        $totalHits
     */
    public function __construct(
        ?Query $query,
        int $totalItems,
        int $totalHits
    ) {
        if ($query instanceof Query) {
            $this->queryUUID = $query->getUUID();
        }

        $this->query = $query;
        $this->totalItems = $totalItems;
        $this->totalHits = $totalHits;
    }

    /**
     * Create by.
     *
     * @param Query|null        $query
     * @param int               $totalItems
     * @param int               $totalHits
     * @param Aggregations|null $aggregations
     * @param string[]          $suggestions
     * @param Item[]            $items
     *
     * @return Result
     */
    public static function create(
        ?Query $query,
        int $totalItems,
        int $totalHits,
        ? Aggregations $aggregations,
        array $suggestions,
        array $items,
        ? string $autocomplete = null,
        array $metadata = []
    ): self {
        $result = new self(
            $query,
            $totalItems,
            $totalHits
        );

        $result->aggregations = $aggregations;
        $result->suggestions = array_combine($suggestions, $suggestions);
        $result->items = $items;
        $result->autocomplete = $autocomplete;
        $result->metadata = $metadata;

        return $result;
    }

    /**
     * Create multiquery Result.
     *
     * @param Result[] $subresults
     *
     * @return Result
     */
    public static function createMultiResult(array $subresults)
    {
        $result = new Result(null, 0, 0);
        $result->subresults = $subresults;

        return $result;
    }

    /**
     * Add item.
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
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get items grouped by type.
     *
     * @return array
     */
    public function getItemsGroupedByTypes(): array
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
    public function getItemsByType(string $type): array
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
    public function getItemsByTypes(array $types): array
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
     * @return Item|null
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
     * @return Aggregations|null
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
     * @return Aggregation|null
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
     * Add suggestion.
     *
     * @param string $suggestion
     */
    public function addSuggestion(string $suggestion)
    {
        $this->suggestions[$suggestion] = $suggestion;
    }

    /**
     * Get suggestions.
     *
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return array_values($this->suggestions);
    }

    /**
     * @param array $suggestions
     */
    public function setSuggestions(array $suggestions)
    {
        $this->suggestions = $suggestions;
    }

    /**
     * @param string $autocomplete
     */
    public function setAutocomplete(string $autocomplete)
    {
        $this->autocomplete = $autocomplete;
    }

    /**
     * @return string|null
     */
    public function getAutocomplete(): ? string
    {
        return $this->autocomplete;
    }

    /**
     * Get query.
     *
     * @return Query|null
     */
    public function getQuery(): ? Query
    {
        return $this->query;
    }

    /**
     * Get query UUID.
     *
     * @return string|null
     */
    public function getQueryUUID(): ? string
    {
        return $this->queryUUID;
    }

    /**
     * Total items.
     *
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
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
     * Get subresults.
     *
     * @return Result[]
     */
    public function getSubresults(): array
    {
        return $this->subresults;
    }

    /**
     * @param array $subResults
     *
     * @return array
     */
    public function setSubResults(array $subResults)
    {
        $this->subresults = $subResults;
    }

    /**
     * Set metadata.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return Query
     */
    public function setMetadataValue(
        string $name,
               $value
    ): self {
        $this->metadata[$name] = $value;

        return $this;
    }

    /**
     * Get metadata.
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get metadata value.
     *
     * @return mixed|null
     */
    public function getMetadataValue(string $name)
    {
        return $this->metadata[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return Result
     */
    public function setContext(array $context): Result
    {
        $this->context = $context;

        return $this;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'query_uuid' => $this->queryUUID,
            'total_items' => $this->totalItems,
            'total_hits' => $this->totalHits,
            'items' => array_map(function (Item $item) {
                return $item->toArray();
            }, $this->items),
            'aggregations' => $this->aggregations instanceof Aggregations
                ? $this->aggregations->toArray()
                : null,
            'suggests' => array_values($this->suggestions),
            'autocomplete' => '' === $this->autocomplete
                ? null
                : $this->autocomplete,
            'subresults' => array_map(function (Result $result) {
                return $result->toArray();
            }, $this->subresults),
            'metadata' => $this->metadata,
            'context' => $this->context,
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
    public static function createFromArray(array $array): self
    {
        $result = self::create(
            null,
            $array['total_items'] ?? 0,
            $array['total_hits'] ?? 0,
            isset($array['aggregations'])
                ? Aggregations::createFromArray($array['aggregations'])
                : null,
            isset($array['suggests'])
                ? array_combine($array['suggests'], $array['suggests'])
                : [],
            array_map(function (array $item) {
                return Item::createFromArray($item);
            }, $array['items'] ?? []),
            $array['autocomplete'] ?? null,
            $array['metadata'] ?? []
        );

        $result->queryUUID = $array['query_uuid'] ?? '';
        $result->subresults = array_filter(
            array_map(function (array $subresultAsArray) {
                return Result::createFromArray($subresultAsArray);
            }, $array['subresults'] ?? [])
        );

        $result->context = $array['context'] ?? [];

        return $result;
    }
}
