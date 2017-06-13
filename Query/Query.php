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

namespace Puntmig\Search\Query;

use Puntmig\Search\Exception\QueryBuildException;
use Puntmig\Search\Geo\LocationRange;
use Puntmig\Search\Model\Coordinate;
use Puntmig\Search\Model\HttpTransportable;
use Puntmig\Search\Model\ItemUUID;

/**
 * Class Query.
 */
class Query implements HttpTransportable
{
    /**
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var Filter[]
     *
     * Filters
     */
    private $filters = [];

    /**
     * @var array
     *
     * Sort
     */
    private $sort;

    /**
     * @var Aggregation[]
     *
     * Aggregations
     */
    private $aggregations = [];

    /**
     * @var int
     *
     * Page
     */
    private $page;

    /**
     * @var int
     *
     * From
     */
    private $from;

    /**
     * @var int
     *
     * Size
     */
    private $size;

    /**
     * @var bool
     *
     * Suggestions enabled
     */
    private $suggestionsEnabled = false;

    /**
     * @var bool
     *
     * Aggregations enabled
     */
    private $aggregationsEnabled = true;

    /**
     * @var string[]
     *
     * Filter fields
     */
    private $filterFields = [];

    /**
     * Construct.
     *
     * @param $queryText
     */
    private function __construct($queryText)
    {
        $this->sortBy(SortBy::SCORE);
        $this->filters['_query'] = Filter::create(
            '',
            [$queryText],
            0,
            Filter::TYPE_QUERY
        );
    }

    /**
     * Create located Query.
     *
     * @param Coordinate $coordinate
     * @param string     $queryText
     * @param int        $page
     * @param int        $size
     *
     * @return Query
     */
    public static function createLocated(
        Coordinate $coordinate,
        string $queryText,
        int $page = 1,
        int $size = 10
    ) {
        $query = self::create(
            $queryText,
            $page,
            $size
        );

        $query->coordinate = $coordinate;

        return $query;
    }

    /**
     * Create new.
     *
     * @param string $queryText
     * @param int    $page
     * @param int    $size
     *
     * @return Query
     */
    public static function create(
        string $queryText,
        int $page = 1,
        int $size = 10
    ) : Query {
        $page = (int) (max(1, $page));
        $query = new self($queryText);
        $query->from = ($page - 1) * $size;
        $query->size = $size;
        $query->page = $page;

        return $query;
    }

    /**
     * Create new query all.
     *
     * @return Query
     */
    public static function createMatchAll()
    {
        return self::create(
            '',
            1,
            1000
        );
    }

    /**
     * Create by uuid.
     *
     * @param ItemUUID $uuid
     *
     * @return Query
     */
    public static function createByUUID(ItemUUID $uuid) : Query
    {
        return self::createByUUIDs([$uuid]);
    }

    /**
     * Create by references.
     *
     * @param ItemUUID[] $uuids
     *
     * @return Query
     */
    public static function createByUUIDs(array $uuids) : Query
    {
        $ids = array_map(function (ItemUUID $uuid) {
            return $uuid->composeUUID();
        }, $uuids);

        $query = self::create('', 1, count($uuids))
            ->disableAggregations()
            ->disableSuggestions();

        $query->filters['_id'] = Filter::create(
            '_id',
            array_unique($ids),
            Filter::AT_LEAST_ONE,
            Filter::TYPE_FIELD
        );

        return $query;
    }

    /**
     * Filter by types.
     *
     * @param array $values
     * @param bool  $aggregate
     *
     * @return Query
     */
    public function filterByTypes(
        array $values,
        bool $aggregate = true
    ) : Query {
        if (!empty($values)) {
            $this->filters['uuid.type'] = Filter::create(
                'uuid.type',
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['uuid.type']);
        }

        if ($aggregate) {
            $this->aggregations['uuid.type'] = Aggregation::create(
                'uuid.type',
                'uuid.type',
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        }

        return $this;
    }

    /**
     * Filter by types.
     *
     * @param array $values
     *
     * @return Query
     */
    public function filterByIds(array $values) : Query
    {
        if (!empty($values)) {
            $this->filters['uuid.id'] = Filter::create(
                'uuid.id',
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['uuid.id']);
        }

        return $this;
    }

    /**
     * Filter by.
     *
     * @param string $filterName
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     * @param bool   $aggregate
     *
     * @return Query
     */
    public function filterBy(
        string $filterName,
        string $field,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true
    ) : Query {
        if (!empty($values)) {
            $this->filters[$filterName] = Filter::create(
                "indexed_metadata.$field",
                $values,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters[$filterName]);
        }

        if ($aggregate) {
            $this->aggregateBy(
                $filterName,
                $field,
                $applicationType
            );
        }

        return $this;
    }

    /**
     * Filter by range.
     *
     * @param string $filterName
     * @param string $field
     * @param array  $options
     * @param array  $values
     * @param int    $applicationType
     * @param bool   $aggregate
     *
     * @return Query
     */
    public function filterByRange(
        string $filterName,
        string $field,
        array $options,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true
    ) : Query {
        $this->filters[$filterName] = Filter::create(
            "indexed_metadata.$field",
            $values,
            $applicationType,
            Filter::TYPE_RANGE
        );

        if ($aggregate) {
            $this->aggregateByRange(
                $filterName,
                $field,
                $options,
                $applicationType
            );
        }

        return $this;
    }

    /**
     * Filter by location.
     *
     * @param LocationRange $locationRange
     *
     * @return Query
     */
    public function filterByLocation(LocationRange $locationRange) : Query
    {
        $this->filters['coordinate'] = Filter::create(
            'coordinate',
            $locationRange->toArray(),
            Filter::AT_LEAST_ONE,
            Filter::TYPE_GEO
        );

        return $this;
    }

    /**
     * Set filter fields.
     *
     * @param string[] $filterFields
     *
     * @return Query
     */
    public function setFilterFields(array $filterFields)
    {
        $this->filterFields = $filterFields;
    }

    /**
     * Get filter fields.
     *
     * @return string[]
     */
    public function getFilterFields() : array
    {
        return $this->filterFields;
    }

    /**
     * Sort by.
     *
     * @param array $sort
     *
     * @return Query
     */
    public function sortBy(array $sort) : Query
    {
        if (isset($sort['_geo_distance'])) {
            if (!$this->coordinate instanceof Coordinate) {
                throw new QueryBuildException('In order to be able to sort by coordinates, you need to create a Query by using Query::createLocated() instead of Query::create()');
            }
            $sort['_geo_distance']['coordinate'] = $this
                ->coordinate
                ->toArray();
        }

        $this->sort = $sort;

        return $this;
    }

    /**
     * Add Families aggregation.
     *
     * @param string $filterName
     * @param string $field
     * @param int    $applicationType
     *
     * @return Query
     */
    public function aggregateBy(
        string $filterName,
        string $field,
        int $applicationType
    ) : Query {
        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            "indexed_metadata.$field",
            $applicationType,
            Filter::TYPE_FIELD
        );

        return $this;
    }

    /**
     * Add tags aggregation.
     *
     * @param string $filterName
     * @param string $field
     * @param array  $options
     * @param int    $applicationType
     *
     * @return Query
     */
    public function aggregateByRange(
        string $filterName,
        string $field,
        array $options,
        int $applicationType
    ) : Query {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            "indexed_metadata.$field",
            $applicationType,
            Filter::TYPE_RANGE,
            $options
        );

        return $this;
    }

    /**
     * Get aggregations.
     *
     * @return Aggregation[]
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Get aggregation.
     *
     * @param string $aggregationName
     *
     * @return null|Aggregation
     */
    public function getAggregation(string $aggregationName) : ? Aggregation
    {
        return $this->aggregations[$aggregationName] ?? null;
    }

    /**
     * Return Querytext.
     *
     * @return string
     */
    public function getQueryText() : string
    {
        return $this
            ->getFilter('_query')
            ->getValues()[0];
    }

    /**
     * Get filters.
     *
     * @return Filter[]
     */
    public function getFilters() : array
    {
        return $this->filters;
    }

    /**
     * Get filter.
     *
     * @param string $filterName
     *
     * @return null|Filter
     */
    public function getFilter(string $filterName) : ? Filter
    {
        return $this->getFilters()[$filterName] ?? null;
    }

    /**
     * Get sort by.
     *
     * @return array
     */
    public function getSortBy() : array
    {
        return $this->sort;
    }

    /**
     * Get from.
     *
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Get page.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Enable suggestions.
     *
     * @return Query
     */
    public function enableSuggestions() : Query
    {
        $this->suggestionsEnabled = true;

        return $this;
    }

    /**
     * Disable suggestions.
     *
     * @return Query
     */
    public function disableSuggestions() : Query
    {
        $this->suggestionsEnabled = false;

        return $this;
    }

    /**
     * Are suggestions enabled?
     *
     * @return bool
     */
    public function areSuggestionsEnabled() : bool
    {
        return $this->suggestionsEnabled;
    }

    /**
     * Enable aggregations.
     *
     * @return Query
     */
    public function enableAggregations() : Query
    {
        $this->aggregationsEnabled = true;

        return $this;
    }

    /**
     * Disable aggregations.
     *
     * @return Query
     */
    public function disableAggregations() : Query
    {
        $this->aggregationsEnabled = false;

        return $this;
    }

    /**
     * Are aggregations enabled?
     *
     * @return bool
     */
    public function areAggregationsEnabled() : bool
    {
        return $this->aggregationsEnabled;
    }

    /**
     * Exclude reference.
     *
     * @param ItemUUID[] $uuids
     *
     * @return Query
     */
    public function excludeUUIDs(array $uuids) : Query
    {
        $this->filters['excluded_ids'] = Filter::create(
            '_id',
            array_map(function (ItemUUID $uuid) {
                return $uuid->composeUUID();
            }, $uuids),
            Filter::EXCLUDE,
            Filter::TYPE_FIELD
        );

        return $this;
    }

    /**
     * Exclude reference.
     *
     * @param ItemUUID $uuid
     *
     * @return Query
     */
    public function excludeUUID(ItemUUID $uuid) : Query
    {
        $this->excludeUUIDs([$uuid]);

        return $this;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        $query = $this->filters['_query'];

        return array_filter([
            'q' => $query->getValues()[0],
            'coordinate' => $this->coordinate instanceof HttpTransportable
                ? $this->coordinate->toArray()
                : null,
            'filters' => array_filter(
                array_map(function (Filter $filter) {
                    return $filter->getFilterType() !== Filter::TYPE_QUERY
                        ? $filter->toArray()
                        : null;
                }, $this->filters)
            ),
            'aggregations' => array_map(function (Aggregation $aggregation) {
                return $aggregation->toArray();
            }, $this->aggregations),
            'sort' => $this->sort,
            'page' => $this->page,
            'size' => $this->size,
            'suggestions_enabled' => $this->suggestionsEnabled,
            'aggregations_enabled' => $this->aggregationsEnabled,
            'filter_fields' => $this->filterFields,
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
     * @return Query
     */
    public static function createFromArray(array $array) : Query
    {
        $query = isset($array['coordinate'])
            ? self::createLocated(
                Coordinate::createFromArray($array['coordinate']),
                $array['q'] ?? '',
                (int) $array['page'],
                (int) $array['size']
            )
            : self::create(
                $array['q'] ?? '',
                (int) $array['page'],
                (int) $array['size']
            );
        $query->aggregations = array_map(function (array $aggregation) {
            return Aggregation::createFromArray($aggregation);
        }, $array['aggregations'] ?? []);

        $query->sort = $array['sort'];
        $query->filters = array_merge(
            $query->filters,
            array_map(function (array $filter) {
                return Filter::createFromArray($filter);
            }, $array['filters'] ?? [])
        );
        $query->suggestionsEnabled = $array['suggestions_enabled'] ?? false;
        $query->aggregationsEnabled = $array['aggregations_enabled'] ?? true;
        $query->filterFields = $array['filter_fields'] ?? [];

        return $query;
    }
}
