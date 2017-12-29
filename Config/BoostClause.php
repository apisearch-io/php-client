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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Config;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * Class BoostClause.
 */
class BoostClause implements HttpTransportable
{
    /**
     * @var string
     *
     * Field
     */
    private $field;

    /**
     * @var string
     *
     * Values
     */
    private $values;

    /**
     * @var float
     *
     * Boost
     */
    private $boost;

    /**
     * BoostClause constructor.
     *
     * @param string   $field
     * @param string[] $values
     * @param float    $boost
     */
    public function __construct(
        string $field,
        array $values,
        float $boost
    ) {
        $this->field = $field;
        $this->values = $values;
        $this->boost = $boost;
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
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Get boost.
     *
     * @return float
     */
    public function getBoost(): float
    {
        return $this->boost;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'values' => $this->values,
            'boost' => $this->boost,
        ];
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
        if (
            !isset($array['field']) ||
            !isset($array['values'])
        ) {
            throw new InvalidFormatException();
        }

        return new self(
            $array['field'],
            $array['values'],
            (float) ($array['boost'] ?? 1)
        );
    }
}
