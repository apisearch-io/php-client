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
 * Class Aggregation.
 */
class Aggregation implements HttpTransportable
{
    /**
     * @var array
     *
     * Sort aggregation by count asc
     */
    const SORT_BY_COUNT_ASC = ['_count', 'asc'];

    /**
     * @var array
     *
     * Sort aggregation by count desc
     */
    const SORT_BY_COUNT_DESC = ['_count', 'desc'];

    /**
     * @var array
     *
     * Sort aggregation by name asc
     */
    const SORT_BY_NAME_ASC = ['_term', 'asc'];

    /**
     * @var array
     *
     * Sort aggregation by name desc
     */
    const SORT_BY_NAME_DESC = ['_term', 'desc'];

    /**
     * @var int
     *
     * No limit
     */
    const NO_LIMIT = 0;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var string
     *
     * Field
     */
    private $field;

    /**
     * @var int
     *
     * Type
     */
    private $applicationType;

    /**
     * @var int
     *
     * Filter type
     */
    private $filterType;

    /**
     * @var string[]
     *
     * Subgroup
     */
    private $subgroup;

    /**
     * @var array
     *
     * Aggregation sort
     */
    private $sort;

    /**
     * @var int
     *
     * Limit
     */
    private $limit;

    /**
     * Aggregation constructor.
     *
     * @param string $name
     * @param string $field
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $subgroup
     * @param array  $sort
     * @param int    $limit
     */
    private function __construct(
        string $name,
        string $field,
        int $applicationType,
        string $filterType,
        array $subgroup,
        array $sort,
        int $limit
    ) {
        $this->name = $name;
        $this->field = $field;
        $this->applicationType = $applicationType;
        $this->filterType = $filterType;
        $this->subgroup = $subgroup;
        $this->sort = $sort;
        $this->limit = $limit;
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
     * Get field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get application type.
     *
     * @return int
     */
    public function getApplicationType(): int
    {
        return $this->applicationType;
    }

    /**
     * Get filter type.
     *
     * @return string
     */
    public function getFilterType(): string
    {
        return $this->filterType;
    }

    /**
     * Get subgroup.
     *
     * @return string[]
     */
    public function getSubgroup(): array
    {
        return $this->subgroup;
    }

    /**
     * Get Sort.
     *
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * Get limit.
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Create.
     *
     * @param string $name
     * @param string $field
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $subgroup
     * @param array  $sort
     * @param int    $limit
     *
     * @return Aggregation
     */
    public static function create(
        string $name,
        string $field,
        int $applicationType,
        string $filterType,
        array $subgroup = [],
        array $sort = self::SORT_BY_COUNT_DESC,
        int $limit = self::NO_LIMIT
    ): Aggregation {
        return new self(
            $name,
            $field,
            $applicationType,
            $filterType,
            $subgroup,
            $sort,
            $limit
        );
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'field' => $this->field === 'uuid.type'
                ? null
                : $this->field,
            'application_type' => $this->applicationType === Filter::AT_LEAST_ONE
                ? null
                : $this->applicationType,
            'filter_type' => $this->filterType === Filter::TYPE_FIELD
                ? null
                : $this->filterType,
            'subgroup' => empty($this->subgroup)
                ? null
                : $this->subgroup,
            'sort' => $this->sort === self::SORT_BY_COUNT_DESC
                ? null
                : $this->sort,
            'limit' => $this->limit === self::NO_LIMIT
                ? null
                : $this->limit,
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
     * @return Aggregation
     */
    public static function createFromArray(array $array): Aggregation
    {
        return self::create(
            $array['name'],
            $array['field'] ?? 'uuid.type',
            (int) ($array['application_type'] ?? Filter::AT_LEAST_ONE),
            $array['filter_type'] ?? Filter::TYPE_FIELD,
            $array['subgroup'] ?? [],
            $array['sort'] ?? self::SORT_BY_COUNT_DESC,
            $array['limit'] ?? self::NO_LIMIT
        );
    }
}

http://search.puntmig.net/?app_id=music&key=1cc7a3e0-bda5-11e7-abc4-cec278b6b50a&query=%7B%22q%22%3A%22%22%2C%22universe_filters%22%3A%5B%5D%2C%22filters%22%3A%5B%5D%2C%22items_promoted%22%3A%5B%5D%2C%22aggregations%22%3A%7B%22year%22%3A%7B%22name%22%3A%22year%22%2C%22field%22%3A%22indexed_metadata.year%22%2C%22applicationType%22%3A8%2C%22filterType%22%3A%22field%22%2C%22subgroup%22%3A%5B%5D%2C%22sort%22%3A%5B%22_term%22%2C%22desc%22%5D%2C%22limit%22%3A0%7D%2C%22genre%22%3A%7B%22name%22%3A%22genre%22%2C%22field%22%3A%22indexed_metadata.genre_data%22%2C%22applicationType%22%3A8%2C%22filterType%22%3A%22field%22%2C%22subgroup%22%3A%5B%5D%2C%22sort%22%3A%5B%22_term%22%2C%22desc%22%5D%2C%22limit%22%3A0%7D%2C%22rating%22%3A%7B%22name%22%3A%22rating%22%2C%22field%22%3A%22indexed_metadata.rating%22%2C%22applicationType%22%3A4%2C%22filterType%22%3A%22field%22%2C%22subgroup%22%3A%5B%5D%2C%22sort%22%3A%5B%22_term%22%2C%22desc%22%5D%2C%22limit%22%3A0%7D%7D%2C%22page%22%3A1%2C%22size%22%3A12%2C%22from%22%3A0%2C%22results_enabled%22%3Atrue%2C%22aggregations_enabled%22%3Atrue%2C%22suggestions_enabled%22%3Afalse%2C%22highlight_enabled%22%3Atrue%2C%22filter_fields%22%3A%5B%5D%2C%22user%22%3Anull%2C%22coordinate%22%3Anull%2C%22sort%22%3A%7B%22_score%22%3A%7B%22order%22%3A%22asc%22%7D%7D%7D
