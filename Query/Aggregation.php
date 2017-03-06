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
     * Aggregation constructor.
     *
     * @param string $name
     * @param string $field
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $subgroup
     */
    private function __construct(
        string $name,
        string $field,
        int $applicationType,
        string $filterType,
        array $subgroup
    ) {
        $this->name = $name;
        $this->field = $field;
        $this->applicationType = $applicationType;
        $this->filterType = $filterType;
        $this->subgroup = $subgroup;
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
     * Create.
     *
     * @param string $name
     * @param string $field
     * @param int    $applicationType
     * @param string $filterType
     * @param array  $subgroup
     *
     * @return self
     */
    public static function create(
        string $name,
        string $field,
        int $applicationType,
        string $filterType,
        array $subgroup = []
    ) : self {
        return new self(
            $name,
            $field,
            $applicationType,
            $filterType,
            $subgroup
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
            'name' => $this->name,
            'field' => $this->field,
            'application_type' => $this->applicationType,
            'filter_type' => $this->filterType,
            'subgroup' => $this->subgroup,
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
            $array['name'],
            $array['field'],
            (int) $array['application_type'],
            $array['filter_type'],
            $array['subgroup'] ?? []
        );
    }
}
