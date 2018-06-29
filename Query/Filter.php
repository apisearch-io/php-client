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

use Apisearch\Model\HttpTransportable;

/**
 * Class Filter.
 */
class Filter implements HttpTransportable
{
    /**
     * @var int
     *
     * Filter by all of them
     */
    const MUST_ALL = 4;

    /**
     * @var int
     *
     * Filter by all of them, with levels
     */
    const MUST_ALL_WITH_LEVELS = 5;

    /**
     * @var int
     *
     * Filter by, at least, one
     */
    const AT_LEAST_ONE = 8;

    /**
     * @var int
     *
     * Exclude all of them
     */
    const EXCLUDE = 16;

    /**
     * @var int
     *
     * Promote all of them
     */
    const PROMOTE = 32;

    /**
     * @var string
     *
     * Filter type field
     */
    const TYPE_FIELD = 'field';

    /**
     * @var string
     *
     * Filter type field
     */
    const TYPE_RANGE = 'range';

    /**
     * @var string
     *
     * Filter type field
     */
    const TYPE_DATE_RANGE = 'date_range';

    /**
     * @var string
     *
     * Filter type field
     */
    const TYPE_GEO = 'geo';

    /**
     * @var string
     *
     * Filter type query
     */
    const TYPE_QUERY = 'query';

    /**
     * @var string
     *
     * Field
     */
    private $field;

    /**
     * @var array
     *
     * Values
     */
    private $values;

    /**
     * @var int
     *
     * Type
     */
    private $applicationType;

    /**
     * @var string
     *
     * Filter type
     */
    private $filterType;

    /**
     * @var array
     *
     * Terms to filter
     */
    private $filterTerms;

    /**
     * Filter constructor.
     *
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $filterTerms
     */
    private function __construct(
        string $field,
        array $values,
        int $applicationType,
        string $filterType,
        array $filterTerms
    ) {
        $this->field = $field;
        $this->values = self::TYPE_GEO !== $filterType
            ? array_values($values)
            : $values;
        $this->applicationType = $applicationType;
        $this->filterType = $filterType;
        $this->filterTerms = $filterTerms;
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
     * Get values.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Has value.
     *
     * @param string $value
     *
     * @return bool
     */
    public function hasValue(string $value): bool
    {
        return in_array($value, $this->getValues());
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
     * Get filter terms.
     *
     * @return array
     */
    public function getFilterTerms(): array
    {
        return $this->filterTerms;
    }

    /**
     * Create filter.
     *
     * @param string $field
     * @param array  $values
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $filterTerms
     *
     * @return Filter
     */
    public static function create(
        string $field,
        array $values,
        int $applicationType,
        string $filterType,
        array $filterTerms = []
    ): self {
        return new self(
            $field,
            $values,
            $applicationType,
            $filterType,
            $filterTerms
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
            'field' => 'uuid.type' === $this->field
                ? null
                : $this->field,
            'values' => $this->values,
            'application_type' => self::AT_LEAST_ONE === $this->applicationType
                ? null
                : $this->applicationType,
            'filter_type' => self::TYPE_FIELD === $this->filterType
                ? null
                : $this->filterType,
            'filter_terms' => $this->filterTerms,
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
     * @return Filter
     */
    public static function createFromArray(array $array): self
    {
        return self::create(
            $array['field'] ?? 'uuid.type',
            $array['values'] ?? [],
            (int) ($array['application_type'] ?? self::AT_LEAST_ONE),
            $array['filter_type'] ?? self::TYPE_FIELD,
            $array['filter_terms'] ?? []
        );
    }

    /**
     * Get path by field.
     *
     * @param string $field
     *
     * @return string
     */
    public static function getFilterPathByField(string $field)
    {
        return in_array($field, ['id', 'type'])
            ? 'uuid.'.$field
            : 'indexed_metadata.'.$field;
    }
}
