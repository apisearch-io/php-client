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

use Puntmig\Search\Exception\UUIDException;

/**
 * Class ItemUUID.
 */
class ItemUUID implements HttpTransportable, UUIDReference
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
     * Type
     */
    private $type;

    /**
     * ItemReference constructor.
     *
     * @param string $id
     * @param string $type
     */
    public function __construct(
        string $id,
        string $type
    ) {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Get product id.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
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
            'type' => $this->type,
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
            !isset($array['id']) ||
            !isset($array['type'])
        ) {
            if (!isset($array['uuid'])) {
                throw UUIDException::createUUIDBadFormatException();
            }
        }

        return new static(
            (string) $array['id'],
            (string) $array['type']
        );
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID() : string
    {
        return "{$this->type}~{$this->id}";
    }
}
