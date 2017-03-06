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

use Puntmig\Search\Model\HttpTransportable;

/**
 * Class Query.
 */
class Query implements HttpTransportable
{
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
     * Create new.
     *
     * @param string $queryText
     * @param int    $page
     * @param int    $size
     *
     * @return self
     */
    public static function create(
        string $queryText,
        int $page = 1,
        int $size = 10
    ) : self {
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
     * @return self
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
     * Filter by families.
     *
     * @param array $families
     * @param int   $applicationType
     *
     * @return self
     */
    public function filterByFamilies(
        array $families,
        int $applicationType = Filter::MUST_ALL
    ) : self {
        if (!empty($families)) {
            $this->filters['family'] = Filter::create(
                'family',
                $families,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['family']);
        }

        return $this;
    }

    /**
     * Filter by types.
     *
     * @param array $types
     * @param int   $applicationType
     *
     * @return self
     */
    public function filterByTypes(
        array $types,
        int $applicationType = Filter::MUST_ALL
    ) : self {
        if (!empty($types)) {
            $this->filters['type'] = Filter::create(
                '_type',
                $types,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['type']);
        }

        return $this;
    }

    /**
     * Filter by categories.
     *
     * @param array $categories
     * @param int   $applicationType
     * @param bool  $aggregate
     *
     * @return self
     */
    public function filterByCategories(
        array $categories,
        int $applicationType = Filter::MUST_ALL_WITH_LEVELS,
        bool $aggregate = true
    ) : self {
        if (!empty($categories)) {
            $this->filters['categories'] = Filter::create(
                'categories.id',
                $categories,
                $applicationType,
                Filter::TYPE_NESTED
            );
        } else {
            unset($this->filters['categories']);
        }

        if ($aggregate) {
            $this->addCategoriesAggregation($applicationType);
        }

        return $this;
    }

    /**
     * Filter by manufacturer.
     *
     * @param array $manufacturers
     * @param int   $applicationType
     * @param bool  $aggregate
     *
     * @return self
     */
    public function filterByManufacturers(
        array $manufacturers,
        int $applicationType = Filter::MUST_ALL,
        bool $aggregate = true
    ) : self {
        if (!empty($manufacturers)) {
            $this->filters['manufacturer'] = Filter::create(
                'manufacturer.id',
                $manufacturers,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['manufacturer']);
        }

        if ($aggregate) {
            $this->addManufacturerAggregation($applicationType);
        }

        return $this;
    }

    /**
     * Filter by brand.
     *
     * @param array $brands
     * @param int   $applicationType
     * @param bool  $aggregate
     *
     * @return self
     */
    public function filterByBrands(
        array $brands,
        int $applicationType = Filter::MUST_ALL,
        bool $aggregate = true
    ) : self {
        if (!empty($brands)) {
            $this->filters['brand'] = Filter::create(
                'brand.id',
                $brands,
                $applicationType,
                Filter::TYPE_FIELD
            );
        } else {
            unset($this->filters['brand']);
        }

        if ($aggregate) {
            $this->addBrandAggregation($applicationType);
        }

        return $this;
    }

    /**
     * Filter by tags.
     *
     * @param string $groupName
     * @param array  $options
     * @param array  $tags
     * @param int    $applicationType
     * @param bool   $aggregate
     *
     * @return self
     */
    public function filterByTags(
        string $groupName,
        array $options,
        array $tags,
        int $applicationType = Filter::MUST_ALL,
        bool $aggregate = true
    ) : self {
        if (!empty($tags)) {
            $this->filters[$groupName] = Filter::create(
                'tags.name',
                $tags,
                $applicationType,
                Filter::TYPE_NESTED,
                [
                    'tags.name',
                    $options,
                ]
            );
        } else {
            unset($this->filters[$groupName]);
        }

        if ($aggregate) {
            $this->addTagsAggregation($groupName, $options, $applicationType);
        }

        return $this;
    }

    /**
     * Filter by range.
     *
     * @param array $options
     * @param array $values
     * @param int   $applicationType
     * @param bool  $aggregate
     *
     * @return self
     */
    public function filterByPriceRange(
        array $options,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true
    ) : self {
        return $this->filterByRange(
            'price',
            'real_price',
            $options,
            $values,
            $applicationType,
            $aggregate
        );
    }

    /**
     * Filter by range.
     *
     * @param string $rangeName
     * @param string $field
     * @param array  $options
     * @param array  $values
     * @param int    $applicationType
     * @param bool   $aggregate
     *
     * @return self
     */
    public function filterByRange(
        string $rangeName,
        string $field,
        array $options,
        array $values,
        int $applicationType = Filter::AT_LEAST_ONE,
        bool $aggregate = true
    ) : self {
        $this->filters[$rangeName] = Filter::create(
            $field,
            $values,
            $applicationType,
            Filter::TYPE_RANGE
        );

        if ($aggregate) {
            $this->addRangeAggregation(
                $rangeName,
                $field,
                $options,
                $applicationType
            );
        }

        return $this;
    }

    /**
     * Sort by.
     *
     * @param array $sort
     *
     * @return self
     */
    public function sortBy(array $sort) : self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Add Manufacturer aggregation.
     *
     * @param int $applicationType
     *
     * @return self
     */
    private function addManufacturerAggregation(int $applicationType) : self
    {
        $this->aggregations['manufacturer'] = Aggregation::create(
            'manufacturer',
            'manufacturer.id|manufacturer.name',
            $applicationType,
            Filter::TYPE_FIELD
        );

        return $this;
    }

    /**
     * Add Brand aggregation.
     *
     * @param int $applicationType
     *
     * @return self
     */
    private function addBrandAggregation(int $applicationType) : self
    {
        $this->aggregations['brand'] = Aggregation::create(
            'brand',
            'brand.id|brand.name',
            $applicationType,
            Filter::TYPE_FIELD
        );

        return $this;
    }

    /**
     * Add categories aggregation.
     *
     * @param int $applicationType
     *
     * @return self
     */
    private function addCategoriesAggregation(int $applicationType) : self
    {
        $this->aggregations['categories'] = Aggregation::create(
            'categories',
            'categories.id|categories.name|categories.level',
            $applicationType,
            Filter::TYPE_NESTED
        );

        return $this;
    }

    /**
     * Add tags aggregation.
     *
     * @param string $groupName
     * @param array  $options
     * @param int    $applicationType
     *
     * @return self
     */
    private function addTagsAggregation(
        string $groupName,
        array $options,
        int $applicationType
    ) : self {
        $this->aggregations[$groupName] = Aggregation::create(
            $groupName,
            'tags.name',
            $applicationType,
            Filter::TYPE_NESTED,
            $options
        );

        return $this;
    }

    /**
     * Add tags aggregation.
     *
     * @param string $rangeName
     * @param string $field
     * @param array  $options
     * @param int    $applicationType
     *
     * @return self
     */
    private function addRangeAggregation(
        string $rangeName,
        string $field,
        array $options,
        int $applicationType
    ) : self {
        if (empty($options)) {
            return $this;
        }

        $this->aggregations[$rangeName] = Aggregation::create(
            $rangeName,
            $field,
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
     * @return Aggregation
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
            ->getField();
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
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        $query = $this->filters['_query'];

        return [
            'q' => $query->getValues()[0],
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
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array) : self
    {
        $query = self::create(
            $array['q'],
            (int) $array['page'],
            (int) $array['size']
        );
        $query->aggregations = array_map(function (array $aggregation) {
            return Aggregation::createFromArray($aggregation);
        }, $array['aggregations']);

        $query->sort = $array['sort'];
        $query->filters = array_merge(
            $query->filters,
            array_map(function (array $filter) {
                return Filter::createFromArray($filter);
            }, $array['filters'])
        );

        return $query;
    }
}
