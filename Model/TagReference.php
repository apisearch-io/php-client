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
 * Class TagReference.
 */
class TagReference implements HttpTransportable, UUIDReference
{
    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * NameReference constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get product name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
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
        if (!isset($array['name'])) {
            return null;
        }

        return new static(
            (string) $array['name']
        );
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID() : string
    {
        return "t~{$this->name}";
    }
}
