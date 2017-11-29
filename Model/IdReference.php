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

/**
 * Class IdReference.
 */
abstract class IdReference implements HttpTransportable, UUIDReference
{
    /**
     * @var string
     *
     * Id
     */
    protected $id;

    /**
     * IdReference constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Get product id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
        if (!isset($array['id'])) {
            return null;
        }

        return new static(
            (string) $array['id']
        );
    }
}
