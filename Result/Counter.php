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
     * @var string
     *
     * Id
     */
    private $id;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var null|string
     *
     * Level
     */
    private $level;

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
     * @param string   $id
     * @param string   $name
     * @param null|int $level
     * @param bool     $used
     * @param int      $n
     */
    private function __construct(
        string $id,
        string $name,
        ? int $level,
        bool $used,
        int $n
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->level = $level;
        $this->used = $used;
        $this->n = $n;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
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
     * Get level.
     *
     * @return null|int
     */
    public function getLevel() : ? int
    {
        return $this->level;
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
     * Create.
     *
     * @param string $name
     * @param int    $n
     * @param array  $activeElements
     *
     * @return self
     */
    public static function createByActiveElements(
        string $name,
        int $n,
        array $activeElements
    ) : self {
        $id = $name;
        $level = null;
        $splittedParts = explode('~~', $name);
        if (count($splittedParts) > 1) {
            $id = $splittedParts[0];
            $name = $splittedParts[1];
        }

        if (count($splittedParts) > 2) {
            $level = (int) $splittedParts[2];
        }

        return new self(
            $id,
            $name,
            $level,
            in_array($id, $activeElements),
            $n
        );
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
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
            $array['id'],
            $array['name'],
            $array['level'],
            $array['used'],
            $array['n']
        );
    }
}
