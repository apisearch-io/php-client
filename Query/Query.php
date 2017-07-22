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
     * @var int
     *
     * Default page
     */
    const DEFAULT_PAGE = 1;

    /**
     * @var int
     *
     * Default size
     */
    const DEFAULT_SIZE = 10;

    /**
     * @var int
     *
     * Infinite size
     */
    const INFINITE_SIZE = 1000;

    /**
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var Filter[]
     *
     * Universe Filters
     */
    private $universeFilters = [];

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
        int $page = self::DEFAULT_PAGE,
        int $size = self::DEFAULT_SIZE
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
        int $page = self::DEFAULT_PAGE,
        int $size = self::DEFAULT_SIZE
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
            self::DEFAULT_PAGE,
            self::INFINITE_SIZE
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

        $query = self::create('', self::DEFAULT_PAGE, count($uuids))
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
     * Filter universe by types.
     *
     * @param array $values
     *
     * @return Query
     */
    public function filterUniverseByTypes(array $values) : Query
    {
        $fieldPath = Filter::getFilterPathByField('type');
        if (!empty($values)) {
            $this->universeFilters['type'] = Filter::create(
                $fieldPath,
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->universeFilters['type']);
        }

        return $this;
    }

    /**
     * Filter by types.
     *
     * @param array $values
     * @param bool  $aggregate
     * @param array $aggregationSort
     *
     * @return Query
     */
    public function filterByTypes(
        array $values,
        bool $aggregate = true,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        $fieldPath = Filter::getFilterPathByField('type');
        if (!empty($values)) {
            $this->filters['type'] = Filter::create(
                $fieldPath,
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['type']);
        }

        if ($aggregate) {
            $this->aggregations['type'] = Aggregation::create(
                'type',
                $fieldPath,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD,
                [],
                $aggregationSort
            );
        }

        return $this;
    }

    /**
     * Filter universe by types.
     *
     * @param array $values
     *
     * @return Query
     */
    public function filterUniverseByIds(array $values) : Query
    {
        $fieldPath = Filter::getFilterPathByField('id');
        if (!empty($values)) {
            $this->universeFilters['id'] = Filter::create(
                $fieldPath,
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->universeFilters['id']);
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
        $fieldPath = Filter::getFilterPathByField('id');
        if (!empty($values)) {
            $this->filters['id'] = Filter::create(
                $fieldPath,
                $values,
                Filter::AT_LEAST_ONE,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['id']);
        }

        return $this;
    }

    /**
     * Filter universe by.
     *
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     *
     * @return Query
     */
    public function filterUniverseBy(
        string $field,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE
    ) : Query {
        $fieldPath = Filter::getFilterPathByField($field);
        if (!empty($values)) {
            $this->universeFilters[$field] = Filter::create(
                $fieldPath,
                $values,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->universeFilters[$field]);
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
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function filterBy(
        string $filterName,
        string $field,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        $fieldPath = Filter::getFilterPathByField($field);
        if (!empty($values)) {
            $this->filters[$filterName] = Filter::create(
                $fieldPath,
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
                $applicationType,
                $aggregationSort
            );
        }

        return $this;
    }

    /**
     * Filter universe by range.
     *
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     * @param string $rangeType
     *
     * @return Query
     */
    public function filterUniverseByRange(
        string $field,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        string $rangeType = Filter::TYPE_RANGE
    ) : Query {
        $fieldPath = Filter::getFilterPathByField($field);
        if (!empty($values)) {
            $this->universeFilters[$field] = Filter::create(
                $fieldPath,
                $values,
                $applicationType,
                $rangeType
            );
        } else {
            unset($this->universeFilters[$field]);
        }

        return $this;
    }

    /**
     * Filter universe by range.
     *
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     *
     * @return Query
     */
    public function filterUniverseByDateRange(
        string $field,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE
    ) : Query {
        return $this->filterUniverseByRange(
            $field,
            $values,
            $applicationType,
            Filter::TYPE_DATE_RANGE
        );
    }

    /**
     * Filter by range.
     *
     * @param string $filterName
     * @param string $field
     * @param array  $options
     * @param array  $values
     * @param int    $applicationType
     * @param string $rangeType
     * @param bool   $aggregate
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function filterByRange(
        string $filterName,
        string $field,
        array $options,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        string $rangeType = Filter::TYPE_RANGE,
        bool $aggregate = true,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        $fieldPath = Filter::getFilterPathByField($field);
        if (!empty($values)) {
            $this->filters[$filterName] = Filter::create(
                $fieldPath,
                $values,
                $applicationType,
                $rangeType
            );
        } else {
            unset($this->filters[$filterName]);
        }

        if ($aggregate) {
            $this->aggregateByRange(
                $filterName,
                $fieldPath,
                $options,
                $applicationType,
                $rangeType,
                $aggregationSort
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
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function filterByDateRange(
        string $filterName,
        string $field,
        array $options,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        return $this->filterByRange(
            $filterName,
            $field,
            $options,
            $values,
            $applicationType,
            Filter::TYPE_DATE_RANGE,
            $aggregate,
            $aggregationSort
        );
    }

    /**
     * Filter universe by location.
     *
     * @param LocationRange $locationRange
     *
     * @return Query
     */
    public function filterUniverseByLocation(LocationRange $locationRange) : Query
    {
        $this->universeFilters['coordinate'] = Filter::create(
            'coordinate',
            $locationRange->toArray(),
            Filter::AT_LEAST_ONE,
            Filter::TYPE_GEO
        );

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
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function aggregateBy(
        string $filterName,
        string $field,
        int $applicationType,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Filter::getFilterPathByField($field),
            $applicationType,
            Filter::TYPE_FIELD,
            [],
            $aggregationSort
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
     * @param string $rangeType
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function aggregateByRange(
        string $filterName,
        string $field,
        array $options,
        int $applicationType,
        string $rangeType = Filter::TYPE_RANGE,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Filter::getFilterPathByField($field),
            $applicationType,
            $rangeType,
            $options,
            $aggregationSort
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
     * @param array  $aggregationSort
     *
     * @return Query
     */
    public function aggregateByDateRange(
        string $filterName,
        string $field,
        array $options,
        int $applicationType,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC
    ) : Query {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Filter::getFilterPathByField($field),
            $applicationType,
            Filter::TYPE_DATE_RANGE,
            $options,
            $aggregationSort
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
     * Get universe filters.
     *
     * @return Filter[]
     */
    public function getUniverseFilters() : array
    {
        return $this->universeFilters;
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
            'q' => !empty($query->getValues()[0])
                ? $query->getValues()[0]
                : null,
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
            'universe_filters' => array_filter(
                array_map(function (Filter $filter) {
                    return $filter->toArray();
                }, $this->universeFilters)
            ),
            'aggregations' => array_map(function (Aggregation $aggregation) {
                return $aggregation->toArray();
            }, $this->aggregations),
            'sort' => $this->sort === SortBy::SCORE
                ? null
                : $this->sort,
            'page' => $this->page === self::DEFAULT_PAGE
                ? null
                : $this->page,
            'size' => $this->size === self::DEFAULT_SIZE
                ? null
                : $this->size,
            'suggestions_enabled' => $this->suggestionsEnabled ?: null,
            'aggregations_enabled' => $this->aggregationsEnabled
                ? null
                : false,
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
                (int) ($array['page'] ?? self::DEFAULT_PAGE),
                (int) ($array['size'] ?? self::DEFAULT_SIZE)
            )
            : self::create(
                $array['q'] ?? '',
                (int) ($array['page'] ?? self::DEFAULT_PAGE),
                (int) ($array['size'] ?? self::DEFAULT_SIZE)
            );
        $query->aggregations = array_map(function (array $aggregation) {
            return Aggregation::createFromArray($aggregation);
        }, $array['aggregations'] ?? []);

        $query->sort = $array['sort'] ?? SortBy::SCORE;
        $query->filters = array_merge(
            $query->filters,
            array_map(function (array $filter) {
                return Filter::createFromArray($filter);
            }, $array['filters'] ?? [])
        );
        $query->universeFilters = array_merge(
            $query->universeFilters,
            array_map(function (array $filter) {
                return Filter::createFromArray($filter);
            }, $array['universe_filters'] ?? [])
        );
        $query->suggestionsEnabled = $array['suggestions_enabled'] ?? false;
        $query->aggregationsEnabled = $array['aggregations_enabled'] ?? true;
        $query->filterFields = $array['filter_fields'] ?? [];

        return $query;
    }
}
