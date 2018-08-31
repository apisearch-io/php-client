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
 * Class IndexUUID.
 */
class IndexUUID implements HttpTransportable, UUIDReference
{
    /**
     * @var string
     *
     * Id
     */
    private $id;

    /**
     * IndexUUID constructor.
     *
     * @param string $id
     */
    private function __construct(string $id)
    {
        if (strpos($id, '_') > 0) {
            var_dump($id);
            throw InvalidFormatException::indexUUIDFormatNotValid();
        }

        $this->id = $id;
    }

    /**
     * Get id.
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
     * @return IndexUUID
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
     * @return IndexUUID
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array): self
    {
        if (!isset($array['id'])) {
            throw InvalidFormatException::indexUUIDFormatNotValid();
        }

        return new self($array['id']);
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID(): string
    {
        return $this->id;
    }
}
