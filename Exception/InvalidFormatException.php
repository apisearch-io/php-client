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

namespace Apisearch\Exception;

/**
 * Class InvalidFormatException.
 */
class InvalidFormatException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 400;
    }

    /**
     * Items representation format not valid.
     *
     * @param mixed $itemsBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function itemsRepresentationNotValid($itemsBeforeHydration): self
    {
        return new self(sprintf('Items representation not valid. Expecting Item array serialized but found "%s" before hydration', substr(
            (string) $itemsBeforeHydration,
            0,
            100
        )));
    }

    /**
     * Items UUID representation format not valid.
     *
     * @param mixed $itemsUUIDBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function itemsUUIDRepresentationNotValid($itemsUUIDBeforeHydration): self
    {
        return new self(sprintf('Items UUID representation not valid. Expecting UUID array serialized but found "%s" before hydration', substr(
            (string) $itemsUUIDBeforeHydration,
            0,
            100
        )));
    }

    /**
     * Query format not valid.
     *
     * @param mixed $queryBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function queryFormatNotValid($queryBeforeHydration): self
    {
        return new self(sprintf('Query Format not valid. Expecting a Query serialized but found "%s" before hydration', substr(
            (string) $queryBeforeHydration,
            0,
            100
        )));
    }
}
