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
 * Class ProductReference.
 */
class ProductReference implements HttpTransportable, UUIDReference
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
     * family
     */
    private $family;

    /**
     * ProductReference constructor.
     *
     * @param string $id
     * @param string $family
     */
    public function __construct(string $id, string $family)
    {
        $this->id = $id;
        $this->family = $family;
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
     * Get family.
     *
     * @return string
     */
    public function getFamily() : string
    {
        return $this->family;
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
            'family' => $this->family,
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
            !isset($array['family'])
        ) {
            return null;
        }

        return new static(
            (string) $array['id'],
            (string) $array['family']
        );
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID()
    {
        return $this->family . '~' . $this->id;
    }
}
