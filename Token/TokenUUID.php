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

namespace Apisearch\Token;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;
use Apisearch\Model\UUIDReference;

/**
 * File header placeholder.
 */
class TokenUUID implements HttpTransportable, UUIDReference
{
    /**
     * @var string
     *
     * Id
     */
    private $id;

    /**
     * TokenUUID constructor.
     *
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
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
     * Create by id.
     *
     * @param string $id
     *
     * @return TokenUUID
     */
    public static function createById(string $id)
    {
        return new self($id);
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
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        if (!isset($array['id'])) {
            throw InvalidFormatException::tokenUUIDFormatNotValid(json_encode($array));
        }

        return self::createById($array['id']);
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID(): string
    {
        return $this->getId();
    }
}
