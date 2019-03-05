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
use Apisearch\Model\Item;

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
    const SCORE = [
        'type' => self::TYPE_SCORE,
    ];

    /**
     * @var array
     *
     * Sort random
     */
    const RANDOM = [
        'type' => self::TYPE_RANDOM,
    ];

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
    const ID_ASC = [
        'field' => 'uuid.id',
        'order' => self::ASC,
    ];

    /**
     * @var array
     *
     * Sort by id DESC
     */
    const ID_DESC = [
        'field' => 'uuid.id',
        'order' => self::DESC,
    ];

    /**
     * @var array
     *
     * Sort by type ASC
     */
    const TYPE_ASC = [
        'field' => 'uuid.type',
        'order' => self::ASC,
    ];

    /**
     * @var array
     *
     * Sort by type DESC
     */
    const TYPE_DESC = [
        'field' => 'uuid.type',
        'order' => self::DESC,
    ];

    /**
     * @var array
     *
     * Sort by location ASC using KM
     */
    const LOCATION_KM_ASC = [
        'type' => self::TYPE_DISTANCE,
        'unit' => 'km',
    ];

    /**
     * @var array
     *
     * Sort by location ASC using Miles
     */
    const LOCATION_MI_ASC = [
        'type' => self::TYPE_DISTANCE,
        'unit' => 'mi',
    ];

    /**
     * @var string
     *
     * Type field
     */
    const TYPE_FIELD = 'field';

    /**
     * @var string
     *
     * Type score
     */
    const TYPE_SCORE = 'score';

    /**
     * @var string
     *
     * Type random
     */
    const TYPE_RANDOM = 'random';

    /**
     * @var string
     *
     * Type distance
     */
    const TYPE_DISTANCE = 'distance';

    /**
     * @var string
     *
     * Type nested
     */
    const TYPE_NESTED = 'nested';

    /**
     * @var string
     *
     * Type function
     */
    const TYPE_FUNCTION = 'function';

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

        $this->sortsBy[] = $value;

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
            'field' => Item::getPathByField($field),
            'order' => $order,
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
            'field' => "indexed_metadata.$field",
            'order' => $order,
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
        $fieldPath = Item::getPathByField($filter->getField());
        $filterAsArray = $filter->toArray();
        $filterAsArray['field'] = $fieldPath;
        $filter = Filter::createFromArray($filterAsArray);
        $this->sortsBy[] = [
            'type' => self::TYPE_NESTED,
            'filter' => $filter,
            'mode' => $mode,
            'field' => "indexed_metadata.$field",
            'order' => $order,
        ];

        return $this;
    }

    /**
     * Sort by function.
     *
     * @param string $function
     * @param string $order
     *
     * @return SortBy
     */
    public function byFunction(
        string $function,
        string $order
    ): SortBy {
        $this->sortsBy[] = [
            'type' => self::TYPE_FUNCTION,
            'function' => $function,
            'order' => $order,
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
            if (self::TYPE_DISTANCE === $sortBy['type']) {
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
            if (self::TYPE_DISTANCE === $sortBy['type']) {
                $sortBy['coordinate'] = $coordinate;
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
                isset($sortBy['filter']) &&
                ($sortBy['filter'] instanceof Filter)
            ) {
                $sortBy['filter'] = $sortBy['filter']->toArray();
            }

            if (
                isset($sortBy['coordinate']) &&
                $sortBy['coordinate'] instanceof Coordinate
            ) {
                $sortBy['coordinate'] = $sortBy['coordinate']->toArray();
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
                isset($element['coordinate']) &&
                is_array($element['coordinate'])
            ) {
                $element['coordinate'] = Coordinate::createFromArray($element['coordinate']);
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
            if (self::TYPE_RANDOM === $sortBy['type']) {
                return true;
            }
        }

        return false;
    }
}
