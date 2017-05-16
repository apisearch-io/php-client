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

namespace Puntmig\Search\Result;

use Puntmig\Search\Model\HttpTransportable;

/**
 * Class Counter.
 */
class Counter implements HttpTransportable
{
    /**
     * @var string[]
     *
     * Values
     */
    private $values;

    /**
     * @var bool
     *
     * Used
     */
    private $used;

    /**
     * @var int
     *
     * N
     */
    private $n;

    /**
     * Counter constructor.
     *
     * @param string[] $values
     * @param bool     $used
     * @param int      $n
     */
    private function __construct(
        array $values,
        bool $used,
        int $n
    ) {
        $this->values = $values;
        $this->used = $used;
        $this->n = $n;
    }

    /**
     * Get id.
     *
     * @return null|string
     */
    public function getId() : ? string
    {
        return $this->values['id'] ?? null;
    }

    /**
     * Get name.
     *
     * @return null|string
     */
    public function getName() : ? string
    {
        return $this->values['name'] ?? null;
    }

    /**
     * Get slug.
     *
     * @return null|string
     */
    public function getSlug() : ? string
    {
        return $this->values['slug'] ?? null;
    }

    /**
     * Get level.
     *
     * @return null|int
     */
    public function getLevel() : ? int
    {
        return (int) ($this->values['level'] ?? 0);
    }

    /**
     * Get values.
     *
     * @return string[]
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * Is used.
     *
     * @return bool
     */
    public function isUsed() : bool
    {
        return $this->used;
    }

    /**
     * Get N.
     *
     * @return int
     */
    public function getN(): int
    {
        return $this->n;
    }

    /**
     * Create or return null if the used element is not valid.
     *
     * @param string $name
     * @param int    $n
     * @param array  $activeElements
     *
     * @return null|Counter
     */
    public static function createByActiveElements(
        string $name,
        int $n,
        array $activeElements
    ) : ? Counter {
        $values = [];
        $splittedParts = explode('~~', $name);
        foreach ($splittedParts as $part) {
            $parts = explode('##', $part);
            if (count($parts) === 2) {
                $values[$parts[0]] = $parts[1];
            } else {
                $values[] = $parts[0];
            }
        }

        if (count($values) == 1) {
            $firstAndUniqueElement = reset($values);
            $values = [
                'id' => $firstAndUniqueElement,
                'name' => $firstAndUniqueElement,
            ];
        }

        if (
            !isset($values['id']) ||
            $values['id'] === 'null'
        ) {
            return null;
        }

        return new self(
            $values,
            in_array($values['id'], $activeElements),
            $n
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
            'values' => $this->values,
            'used' => $this->used,
            'n' => $this->n,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array)
    {
        return new self(
            $array['values'],
            $array['used'],
            $array['n']
        );
    }
}
