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
use Apisearch\Model\Coordinate;
use Apisearch\Model\HttpTransportable;

/**
 * Class SortBy.
 */
class SortBy implements HttpTransportable
{
    /**
     * @var array
     *
     * Sort by score
     */
    const SCORE = ['_score' => ['order' => self::ASC]];

    /**
     * @var array
     *
     * Sort random
     */
    const RANDOM = ['random' => true];

    /**
     * @var array
     *
     * Sort al-tun-tun
     */
    const AL_TUN_TUN = self::RANDOM;

    /**
     * @var array
     *
     * Sort by id ASC
     */
    const ID_ASC = ['uuid.id' => ['order' => self::ASC]];

    /**
     * @var array
     *
     * Sort by id DESC
     */
    const ID_DESC = ['uuid.id' => ['order' => self::DESC]];

    /**
     * @var array
     *
     * Sort by type ASC
     */
    const TYPE_ASC = ['uuid.type' => ['order' => self::ASC]];

    /**
     * @var array
     *
     * Sort by type DESC
     */
    const TYPE_DESC = ['uuid.type' => ['order' => self::DESC]];

    /**
     * @var array
     *
     * Sort by location ASC using KM
     */
    const LOCATION_KM_ASC = ['_geo_distance' => ['order' => self::ASC, 'unit' => 'km']];

    /**
     * @var array
     *
     * Sort by location ASC using Miles
     */
    const LOCATION_MI_ASC = ['_geo_distance' => ['order' => self::ASC, 'unit' => 'mi']];
    /**
     * @var array
     *
     * Sort by id ASC
     */
    const OCCURRED_ON_ASC = ['indexed_metadata.occurred_on' => ['order' => self::ASC]];

    /**
     * @var array
     *
     * Sort by id DESC
     */
    const OCCURRED_ON_DESC = ['indexed_metadata.occurred_on' => ['order' => self::DESC]];

    /**
     * @var int
     *
     * Type field
     */
    const TYPE_FIELD = 1;

    /**
     * @var int
     *
     * Type nested
     */
    const TYPE_NESTED = 2;

    /**
     * @var string
     *
     * Asc
     */
    const ASC = 'asc';

    /**
     * @var string
     *
     * Desc
     */
    const DESC = 'desc';

    /**
     * @var array
     *
     * Sorts by
     */
    private $sortsBy = [];

    /**
     * @var string
     *
     * Mode avg
     */
    const MODE_AVG = 'avg';

    /**
     * @var string
     *
     * Mode sum
     */
    const MODE_SUM = 'sum';

    /**
     * @var string
     *
     * Mode min
     */
    const MODE_MIN = 'min';

    /**
     * @var string
     *
     * Mode max
     */
    const MODE_MAX = 'max';

    /**
     * @var string
     *
     * Mode
     */
    const MODE_MEDIAN = 'median';

    /**
     * Create sort by.
     *
     * @return SortBy
     */
    public static function create(): SortBy
    {
        return new self();
    }

    /**
     * Create sort by with passed simple fields and values.
     *
     * SortBy::byFieldsValues([
     *      ['brand' => 'asc'],
     *      ['category' => 'desc'],
     * ]);
     *
     * @param array $shortSortByElements
     *
     * @return SortBy
     */
    public static function byFieldsValues(array $shortSortByElements): SortBy
    {
        $sortBy = self::create();
        foreach ($shortSortByElements as $field => $order) {
            $sortBy->byFieldValue($field, $order);
        }

        return $sortBy;
    }

    /**
     * Get all.
     *
     * @return array
     */
    public function all(): array
    {
        return empty($this->sortsBy)
            ? [self::SCORE]
            : array_values($this->sortsBy);
    }

    /**
     * Sort by value.
     *
     * @param array $value
     *
     * @return SortBy
     */
    public function byValue(array $value): SortBy
    {
        if (
            self::SCORE !== $value &&
            self::RANDOM !== $value
        ) {
            $value['type'] = $value['type'] ?? self::TYPE_FIELD;
        }

        if (self::SCORE !== $value) {
            $this->sortsBy[] = $value;
        }

        return $this;
    }

    /**
     * Sort by field.
     *
     * @param string $field
     * @param string $order
     *
     * @return SortBy
     */
    public function byFieldValue(
        string $field,
        string $order
    ): SortBy {
        $this->sortsBy[] = [
            'type' => self::TYPE_FIELD,
            "indexed_metadata.$field" => [
                'order' => $order,
            ],
        ];

        return $this;
    }

    /**
     * Sort by nested field and filter.
     *
     * @param string $field
     * @param string $order
     * @param string $mode
     *
     * @return SortBy
     */
    public function byNestedField(
        string $field,
        string $order,
        string $mode = self::MODE_AVG
    ): SortBy {
        $this->sortsBy[] = [
            'type' => self::TYPE_NESTED,
            'mode' => $mode,
            "indexed_metadata.$field" => [
                'order' => $order,
            ],
        ];

        return $this;
    }

    /**
     * Sort by nested field and filter.
     *
     * @param string $field
     * @param string $order
     * @param Filter $filter
     * @param string $mode
     *
     * @return SortBy
     */
    public function byNestedFieldAndFilter(
        string $field,
        string $order,
        Filter $filter,
        string $mode = self::MODE_AVG
    ): SortBy {
        $fieldPath = Filter::getFilterPathByField($filter->getField());
        $filterAsArray = $filter->toArray();
        $filterAsArray['field'] = $fieldPath;
        $filter = Filter::createFromArray($filterAsArray);
        $this->sortsBy[] = [
            'type' => self::TYPE_NESTED,
            'filter' => $filter,
            'mode' => $mode,
            "indexed_metadata.$field" => [
                'order' => $order,
            ],
        ];

        return $this;
    }

    /**
     * Is sorted by geo distance.
     *
     * @return bool
     */
    public function isSortedByGeoDistance(): bool
    {
        foreach ($this->sortsBy as $sortBy) {
            if (isset($sortBy['_geo_distance'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set coordinate.
     *
     * @param Coordinate $coordinate
     *
     * @return SortBy
     */
    public function setCoordinate(Coordinate $coordinate)
    {
        $this->sortsBy = array_map(function (array $sortBy) use ($coordinate) {
            if (isset($sortBy['_geo_distance'])) {
                $sortBy['_geo_distance']['coordinate'] = $coordinate;
            }

            return $sortBy;
        }, $this->sortsBy);

        return $this;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (array $sortBy) {
            if (
                isset($sortBy['type']) &&
                self::TYPE_FIELD == $sortBy['type']
            ) {
                unset($sortBy['type']);
            }

            if (
                isset($sortBy['filter']) &&
                ($sortBy['filter'] instanceof Filter)
            ) {
                $sortBy['filter'] = $sortBy['filter']->toArray();
            }

            if (
                isset($sortBy['_geo_distance']) &&
                isset($sortBy['_geo_distance']['coordinate']) &&
                ($sortBy['_geo_distance']['coordinate'] instanceof Coordinate)) {
                $sortBy['_geo_distance']['coordinate'] = $sortBy['_geo_distance']['coordinate']->toArray();
            }

            return $sortBy;
        }, $this->sortsBy);
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        $sortBy = self::create();
        $sortBy->sortsBy = array_map(function (array $element) {
            if (
                self::RANDOM !== $element &&
                self::SCORE !== $element
            ) {
                $element['type'] = $element['type'] ?? SortBy::TYPE_FIELD;
            }

            if (isset($element['filter'])) {
                $element['filter'] = Filter::createFromArray($element['filter']);
            }

            if (
                isset($element['_geo_distance']) &&
                isset($element['_geo_distance']['coordinate']) &&
                is_array($element['_geo_distance']['coordinate'])) {
                $element['_geo_distance']['coordinate'] = Coordinate::createFromArray($element['_geo_distance']['coordinate']);
            }

            return $element;
        }, $array);

        return $sortBy;
    }

    /**
     * Has random sort.
     *
     * @return bool
     */
    public function hasRandomSort()
    {
        foreach ($this->sortsBy as $sortBy) {
            if (self::RANDOM === $sortBy) {
                return true;
            }
        }

        return false;
    }
}
