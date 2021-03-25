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
use Apisearch\Model\HttpTransportable;

/**
 * Class Aggregation.
 */
class Aggregation implements HttpTransportable
{
    /**
     * @var array
     */
    const SORT_BY_COUNT_ASC = ['_count', 'asc'];

    /**
     * @var array
     */
    const SORT_BY_COUNT_DESC = ['_count', 'desc'];

    /**
     * @var array
     */
    const SORT_BY_NAME_ASC = ['_term', 'asc'];

    /**
     * @var array
     */
    const SORT_BY_NAME_DESC = ['_term', 'desc'];

    /**
     * @var int
     */
    const NO_LIMIT = 0;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $field;

    /**
     * @var int
     */
    private $applicationType;

    /**
     * @var int
     */
    private $filterType;

    /**
     * @var string[]
     */
    private $subgroup = [];

    /**
     * @var array
     */
    private $sort;

    /**
     * @var int
     */
    private $limit;

    /**
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
    ): self {
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
            'field' => 'uuid.type' === $this->field
                ? null
                : $this->field,
            'application_type' => Filter::AT_LEAST_ONE === $this->applicationType
                ? null
                : $this->applicationType,
            'filter_type' => Filter::TYPE_FIELD === $this->filterType
                ? null
                : $this->filterType,
            'subgroup' => empty($this->subgroup)
                ? null
                : $this->subgroup,
            'sort' => self::SORT_BY_COUNT_DESC === $this->sort
                ? null
                : $this->sort,
            'limit' => self::NO_LIMIT === $this->limit
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
    public static function createFromArray(array $array): self
    {
        if (empty($array['name'])) {
            throw InvalidFormatException::queryFormatNotValid($array);
        }

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
