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

namespace Apisearch\Model;

use Apisearch\Exception\InvalidFormatException;

/**
 * Class Changes.
 */
class Changes implements HttpTransportable
{
    /**
     * @var int
     *
     * Value change
     */
    const TYPE_VALUE = 1;

    /**
     * @var int
     *
     * Literal change
     */
    const TYPE_LITERAL = 4;

    /**
     * @var int
     *
     * Add the element
     */
    const TYPE_ARRAY_ELEMENT_UPDATE = 8;

    /**
     * @var int
     *
     * Add the element
     */
    const TYPE_ARRAY_ELEMENT_ADD = 16;

    /**
     * @var int
     *
     * Delete the element
     */
    const TYPE_ARRAY_ELEMENT_DELETE = 32;

    /**
     * @var int
     *
     * Delete the element
     */
    const TYPE_ARRAY_EXPECTS_ELEMENT = 24;

    /**
     * @var int
     *
     * Value change
     */
    const TYPE_ARRAY = 56;

    /**
     * @var array
     *
     * Changes
     */
    private $changes = [];

    /**
     * Add change.
     *
     * @param string $field
     * @param mixed  $value
     * @param int    $type
     *
     * @return self
     */
    public function addChange(
        string $field,
        $value,
        int $type = self::TYPE_VALUE
    ): self {
        $this->changes[] = [
            'field' => $field,
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Add change inside a list.
     *
     * @param string $field
     * @param string $condition
     * @param mixed  $value
     * @param int    $type
     *
     * @return self
     */
    public function updateElementFromList(
        string $field,
        string $condition,
        $value,
        int $type
    ): self {
        $this->changes[] = [
            'field' => $field,
            'type' => $type | self::TYPE_ARRAY_ELEMENT_UPDATE,
            'condition' => $condition,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Add change inside a list.
     *
     * @param string $field
     * @param mixed  $value
     * @param int    $type
     *
     * @return self
     */
    public function addElementInList(
        string $field,
        $value,
        int $type
    ): self {
        $this->changes[] = [
            'field' => $field,
            'type' => $type | self::TYPE_ARRAY_ELEMENT_ADD,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Delete change from a list.
     *
     * @param string $field
     * @param string $condition
     *
     * @return self
     */
    public function deleteElementFromList(
        string $field,
        string $condition
    ): self {
        $this->changes[] = [
            'field' => $field,
            'type' => self::TYPE_ARRAY_ELEMENT_DELETE,
            'condition' => $condition,
        ];

        return $this;
    }

    /**
     * Get changes.
     *
     * @return array
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Create empty.
     *
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->changes;
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
        $changes = new self();
        $changes->changes = $array;

        return $changes;
    }
}
