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

namespace Apisearch\Model;

use Apisearch\Exception\InvalidFormatException;

/**
 * Class User.
 */
class User implements HttpTransportable
{
    /**
     * @var string
     *
     * User id
     */
    private $id;

    /**
     * @var array
     *
     * Attributes
     */
    private $attributes;

    /**
     * User constructor.
     *
     * @param string $id
     * @param array  $attributes
     */
    public function __construct(
        string $id,
        array $attributes = []
    ) {
        $this->id = $id;
        $this->attributes = $attributes;
    }

    /**
     * Get Id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get Attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
            'attributes' => $this->attributes,
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
        if (
            !isset($array['id'])
        ) {
            throw InvalidFormatException::userFormatNotValid([]);
        }

        return self::createFromId(
            $array['id'],
            $array['attributes'] ?? []
        );
    }

    /**
     * @param string $id
     * @param array  $attributes
     *
     * @return User
     */
    public static function createFromId(string $id, array $attributes = []): User
    {
        return new self(
            $id,
            $attributes
        );
    }
}
