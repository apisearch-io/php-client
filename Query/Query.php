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

namespace Apisearch\Query;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Geo\LocationRange;
use Apisearch\Model\Coordinate;
use Apisearch\Model\HttpTransportable;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;

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
     * @var float
     *
     * No min score
     */
    const NO_MIN_SCORE = 0.0;

    /**
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var string
     *
     * Fields
     */
    private $fields = [];

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
     * @var ItemUUID[]
     *
     * Items Promoted
     */
    private $itemsPromoted = [];

    /**
     * @var SortBy
     *
     * Sort by
     */
    private $sortBy;

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
     * Results enabled
     */
    private $resultsEnabled = true;

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
     * @var bool
     *
     * Highlights enabled
     */
    private $highlightEnabled = false;

    /**
     * @var string[]
     *
     * Filter fields
     */
    private $filterFields = [];

    /**
     * @var ScoreStrategies
     *
     * Score strategies
     */
    private $scoreStrategies;

    /**
     * @var float|string|array
     *
     * Fuzziness
     */
    private $fuzziness;

    /**
     * @var float
     *
     * Min score
     */
    private $minScore = self::NO_MIN_SCORE;

    /**
     * @var User
     *
     * User associated to query
     */
    private $user;

    /**
     * Construct.
     *
     * @param string $queryText
     */
    private function __construct(string $queryText)
    {
        $this->sortBy = SortBy::create();
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
    ): self {
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
    public static function createMatchAll(): self
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
    public static function createByUUID(ItemUUID $uuid): self
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
    public static function createByUUIDs(array $uuids): self
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
     * Select fields.
     *
     * @param string[] $fields
     *
     * @return Query
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get fields.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Filter universe by types.
     *
     * @param array $values
     *
     * @return Query
     */
    public function filterUniverseByTypes(array $values): self
    {
        $fieldPath = Item::getPathByField('type');
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
    ): self {
        $fieldPath = Item::getPathByField('type');
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
    public function filterUniverseByIds(array $values): self
    {
        $fieldPath = Item::getPathByField('id');
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
    public function filterByIds(array $values): self
    {
        $fieldPath = Item::getPathByField('id');
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
    ): self {
        $fieldPath = Item::getPathByField($field);
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
    ): self {
        $fieldPath = Item::getPathByField($field);
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
    ): self {
        $fieldPath = Item::getPathByField($field);
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
    ): self {
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
    ): self {
        $fieldPath = Item::getPathByField($field);
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
    ): self {
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
    public function filterUniverseByLocation(LocationRange $locationRange): self
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
     * Set filter fields.
     *
     * @param string[] $filterFields
     *
     * @return Query
     */
    public function setFilterFields(array $filterFields)
    {
        $this->filterFields = $filterFields;

        return $this;
    }

    /**
     * Get filter fields.
     *
     * @return string[]
     */
    public function getFilterFields(): array
    {
        return $this->filterFields;
    }

    /**
     * Sort by.
     *
     * @param SortBy $sortBy
     *
     * @return Query
     */
    public function sortBy(SortBy $sortBy): self
    {
        if ($sortBy->isSortedByGeoDistance()) {
            if (!$this->coordinate instanceof Coordinate) {
                throw InvalidFormatException::querySortedByDistanceWithoutCoordinate();
            }

            $sortBy->setCoordinate($this->coordinate);
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * Add Families aggregation.
     *
     * @param string $filterName
     * @param string $field
     * @param int    $applicationType
     * @param array  $aggregationSort
     * @param int    $limit
     *
     * @return Query
     */
    public function aggregateBy(
        string $filterName,
        string $field,
        int $applicationType,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC,
        int $limit = Aggregation::NO_LIMIT
    ): self {
        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Item::getPathByField($field),
            $applicationType,
            Filter::TYPE_FIELD,
            [],
            $aggregationSort,
            $limit
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
     * @param int    $limit
     *
     * @return Query
     */
    public function aggregateByRange(
        string $filterName,
        string $field,
        array $options,
        int $applicationType,
        string $rangeType = Filter::TYPE_RANGE,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC,
        int $limit = Aggregation::NO_LIMIT
    ): self {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Item::getPathByField($field),
            $applicationType,
            $rangeType,
            $options,
            $aggregationSort,
            $limit
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
     * @param int    $limit
     *
     * @return Query
     */
    public function aggregateByDateRange(
        string $filterName,
        string $field,
        array $options,
        int $applicationType,
        array $aggregationSort = Aggregation::SORT_BY_COUNT_DESC,
        int $limit = Aggregation::NO_LIMIT
    ): self {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$filterName] = Aggregation::create(
            $filterName,
            Item::getPathByField($field),
            $applicationType,
            Filter::TYPE_DATE_RANGE,
            $options,
            $aggregationSort,
            $limit
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
     * @return Aggregation|null
     */
    public function getAggregation(string $aggregationName): ? Aggregation
    {
        return $this->aggregations[$aggregationName] ?? null;
    }

    /**
     * Return Querytext.
     *
     * @return string
     */
    public function getQueryText(): string
    {
        return (
            isset($this->filters['_query']) &&
            $this->filters['_query'] instanceof Filter
        )
            ? $this->filters['_query']->getValues()[0]
            : '';
    }

    /**
     * Get universe filters.
     *
     * @return Filter[]
     */
    public function getUniverseFilters(): array
    {
        return $this->universeFilters;
    }

    /**
     * Get universe filter.
     *
     * @param string $universeFilterName
     *
     * @return Filter|null
     */
    public function getUniverseFilter(string $universeFilterName): ? Filter
    {
        return $this->getUniverseFilters()[$universeFilterName] ?? null;
    }

    /**
     * Get filters.
     *
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get filter.
     *
     * @param string $filterName
     *
     * @return Filter|null
     */
    public function getFilter(string $filterName): ? Filter
    {
        return $this->getFilters()[$filterName] ?? null;
    }

    /**
     * Get filter.
     *
     * @param string $fieldName
     *
     * @return Filter|null
     */
    public function getFilterByField(string $fieldName): ? Filter
    {
        $fieldPath = Item::getPathByField($fieldName);
        foreach ($this->getFilters() as $filter) {
            if ($fieldPath === $filter->getField()) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * Get sort by.
     *
     * @return SortBy
     */
    public function getSortBy(): SortBy
    {
        return $this->sortBy;
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
     * Enable results.
     *
     * @return Query
     */
    public function enableResults(): self
    {
        $this->resultsEnabled = true;

        return $this;
    }

    /**
     * Disable results.
     *
     * @return Query
     */
    public function disableResults(): self
    {
        $this->resultsEnabled = false;

        return $this;
    }

    /**
     * Are results enabled.
     *
     * @return bool
     */
    public function areResultsEnabled(): bool
    {
        return $this->resultsEnabled;
    }

    /**
     * Enable suggestions.
     *
     * @return Query
     */
    public function enableSuggestions(): self
    {
        $this->suggestionsEnabled = true;

        return $this;
    }

    /**
     * Disable suggestions.
     *
     * @return Query
     */
    public function disableSuggestions(): self
    {
        $this->suggestionsEnabled = false;

        return $this;
    }

    /**
     * Are suggestions enabled?
     *
     * @return bool
     */
    public function areSuggestionsEnabled(): bool
    {
        return $this->suggestionsEnabled;
    }

    /**
     * Enable aggregations.
     *
     * @return Query
     */
    public function enableAggregations(): self
    {
        $this->aggregationsEnabled = true;

        return $this;
    }

    /**
     * Disable aggregations.
     *
     * @return Query
     */
    public function disableAggregations(): self
    {
        $this->aggregationsEnabled = false;

        return $this;
    }

    /**
     * Are aggregations enabled?
     *
     * @return bool
     */
    public function areAggregationsEnabled(): bool
    {
        return $this->aggregationsEnabled;
    }

    /**
     * Enable highlights.
     *
     * @return Query
     */
    public function enableHighlights()
    {
        $this->highlightEnabled = true;

        return $this;
    }

    /**
     * Enable highlights.
     *
     * @return Query
     */
    public function disableHighlights()
    {
        $this->highlightEnabled = false;

        return $this;
    }

    /**
     * Get highlight fields.
     *
     * @return bool
     */
    public function areHighlightEnabled(): bool
    {
        return $this->highlightEnabled;
    }

    /**
     * Prioritize some UUIDs.
     *
     * @param ItemUUID $itemUUID
     *
     * @return Query
     */
    public function promoteUUID(ItemUUID $itemUUID): self
    {
        $this->itemsPromoted[] = $itemUUID;

        return $this;
    }

    /**
     * Prioritize some UUIDs.
     *
     * @param ItemUUID[] $uuids
     *
     * @return Query
     */
    public function promoteUUIDs(array $uuids): self
    {
        $this->itemsPromoted = $uuids;

        return $this;
    }

    /**
     * Get ItemsPromoted.
     *
     * @return ItemUUID[]
     */
    public function getItemsPromoted(): array
    {
        return $this->itemsPromoted;
    }

    /**
     * Exclude reference.
     *
     * @param ItemUUID[] $uuids
     *
     * @return Query
     */
    public function excludeUUIDs(array $uuids): self
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
    public function excludeUUID(ItemUUID $uuid): self
    {
        $this->excludeUUIDs([$uuid]);

        return $this;
    }

    /**
     * Get score strategies.
     *
     * @return ScoreStrategies|null
     */
    public function getScoreStrategies(): ? ScoreStrategies
    {
        return $this->scoreStrategies;
    }

    /**
     * Set score strategies.
     *
     * @param ScoreStrategies $scoreStrategies
     *
     * @return Query
     */
    public function setScoreStrategies(ScoreStrategies $scoreStrategies)
    {
        $this->scoreStrategies = $scoreStrategies;

        return $this;
    }

    /**
     * Get Fuzziness.
     *
     * @return float|string|array|null
     */
    public function getFuzziness()
    {
        return $this->fuzziness;
    }

    /**
     * Set Fuzziness.
     *
     * @param float|string|array $fuzziness
     *
     * @return Query
     */
    public function setFuzziness($fuzziness)
    {
        $this->fuzziness = $fuzziness;

        return $this;
    }

    /**
     * Set auto Fuzziness.
     *
     * @return Query
     */
    public function setAutoFuzziness()
    {
        $this->fuzziness = 'AUTO';

        return $this;
    }

    /**
     * Get MinScore.
     *
     * @return float
     */
    public function getMinScore(): float
    {
        return $this->minScore;
    }

    /**
     * Set MinScore.
     *
     * @param float $minScore
     *
     * @return Query
     */
    public function setMinScore(float $minScore)
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * Query by user.
     *
     * @param User $user
     *
     * @return Query
     */
    public function byUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Query anonymously.
     *
     * @return Query
     */
    public function anonymously(): self
    {
        $this->user = null;

        return $this;
    }

    /**
     * Get User.
     *
     * @return User|null
     */
    public function getUser(): ? User
    {
        return $this->user;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'q' => '' !== $this->getQueryText()
                ? $this->getQueryText()
                : null,
            'fields' => $this->getFields(),
            'coordinate' => $this->coordinate instanceof HttpTransportable
                ? $this->coordinate->toArray()
                : null,
            'filters' => array_filter(
                array_map(function (Filter $filter) {
                    return Filter::TYPE_QUERY !== $filter->getFilterType()
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
            'sort' => $this->sortBy instanceof SortBy
                ? $this->sortBy->toArray()
                : null,
            'page' => self::DEFAULT_PAGE === $this->page
                ? null
                : $this->page,
            'size' => self::DEFAULT_SIZE === $this->size
                ? null
                : $this->size,
            'results_enabled' => $this->resultsEnabled
                ? null
                : false,
            'suggestions_enabled' => $this->suggestionsEnabled ?: null,
            'highlight_enabled' => $this->highlightEnabled ?: null,
            'aggregations_enabled' => $this->aggregationsEnabled
                ? null
                : false,
            'filter_fields' => $this->filterFields,
            'score_strategies' => $this->scoreStrategies instanceof ScoreStrategies
                ? $this->scoreStrategies->toArray()
                : null,
            'fuzziness' => $this->fuzziness,
            'min_score' => ($this->minScore > self::NO_MIN_SCORE)
                ? $this->minScore
                : null,
            'user' => ($this->user instanceof User)
                ? $this->user->toArray()
                : null,
            'items_promoted' => array_filter(
                array_map(function (ItemUUID $itemUUID) {
                    return $itemUUID->toArray();
                }, $this->itemsPromoted)
            ),
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
    public static function createFromArray(array $array): self
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
        $query->fields = $array['fields'] ?? [];
        $query->aggregations = array_map(function (array $aggregation) {
            return Aggregation::createFromArray($aggregation);
        }, $array['aggregations'] ?? []);

        $query->sortBy = SortBy::createFromArray($array['sort'] ?? []);
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
        $query->resultsEnabled = $array['results_enabled'] ?? true;
        $query->suggestionsEnabled = $array['suggestions_enabled'] ?? false;
        $query->aggregationsEnabled = $array['aggregations_enabled'] ?? true;
        $query->highlightEnabled = $array['highlight_enabled'] ?? false;
        $query->itemsPromoted = array_values(array_map(function (array $itemUUID) {
            return ItemUUID::createFromArray($itemUUID);
        }, $array['items_promoted'] ?? []));
        $query->fuzziness = $array['fuzziness'] ?? null;
        $query->filterFields = $array['filter_fields'] ?? [];
        $query->scoreStrategies = isset($array['score_strategies'])
            ? ScoreStrategies::createFromArray($array['score_strategies'])
            : null;
        $query->minScore = $array['min_score'] ?? self::NO_MIN_SCORE;

        if (isset($array['user'])) {
            $query->user = User::createFromArray($array['user']);
        }

        return $query;
    }
}
