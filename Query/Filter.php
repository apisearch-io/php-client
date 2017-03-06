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
 * Class Filter.
 */
class Filter implements HttpTransportable
{
    /**
     * @var int
     *
     * Filter
     */
    const MUST_ALL = 1;

    /**
     * @var int
     *
     * Accumulative
     */
    const AT_LEAST_ONE = 2;

    /**
     * @var int
     *
     * Filter
     */
    const MUST_ALL_WITH_LEVELS = 5;

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
    const TYPE_NESTED = 'nested';

    /**
     * @var string
     *
     * Filter type field
     */
    const TYPE_RANGE = 'range';

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
        $this->values = $values;
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
    public function hasValue(string $value) : bool
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
     * @return self
     */
    public static function create(
        string $field,
        array $values,
        int $applicationType,
        string $filterType,
        array $filterTerms = []
    ) : self {
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
    public function toArray() : array
    {
        return [
            'field' => $this->field,
            'values' => $this->values,
            'application_type' => $this->applicationType,
            'filter_type' => $this->filterType,
            'filter_terms' => $this->filterTerms,
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
        return self::create(
            $array['field'],
            $array['values'] ?? [],
            (int) $array['application_type'],
            $array['filter_type'],
            $array['filter_terms'] ?? []
        );
    }
}
