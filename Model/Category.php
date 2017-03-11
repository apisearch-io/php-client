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

namespace Puntmig\Search\Model;

/**
 * Class Category.
 */
class Category extends IdNameWrapper implements WithLevel
{
    /**
     * @var string
     *
     * Name
     */
    const TYPE = 'category';

    /**
     * @var int
     *
     * Level
     */
    private $level;

    /**
     * Category constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $slug
     * @param int    $level
     */
    public function __construct(
        string $id,
        string $name,
        string $slug,
        int $level = 1
    ) {
        parent::__construct($id, $name, $slug);

        $this->level = $level;
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return static
     */
    public static function createFromArray(array $array)
    {
        if (
            !isset($array['id']) ||
            !isset($array['name']) ||
            !isset($array['slug'])
        ) {
            return null;
        }

        return new static(
            (string) $array['id'],
            (string) $array['name'],
            (string) $array['slug'],
            (int) $array['level'] ?? 1
        );
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'level' => $this->level,
        ];
    }
}
