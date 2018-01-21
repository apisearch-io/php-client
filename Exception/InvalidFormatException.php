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

    /**
     * Config format not valid.
     *
     * @param mixed $configBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function configFormatNotValid($configBeforeHydration): self
    {
        return new self(sprintf('Config Format not valid. Expecting a Config serialized but found "%s" before hydration', substr(
            (string) $configBeforeHydration,
            0,
            100
        )));
    }

    /**
     * Token format not valid.
     *
     * @param mixed $tokenBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function tokenFormatNotValid($tokenBeforeHydration): self
    {
        return new self(sprintf('Token Format not valid. Expecting a Token serialized but found "%s" before hydration', substr(
            (string) $tokenBeforeHydration,
            0,
            100
        )));
    }

    /**
     * Token uuid format not valid.
     *
     * @param mixed $tokenUUIDBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function tokenUUIDFormatNotValid($tokenUUIDBeforeHydration): self
    {
        return new self(sprintf('Token UUID Format not valid. Expecting a TokenUUID serialized but found "%s" before hydration', substr(
            (string) $tokenUUIDBeforeHydration,
            0,
            100
        )));
    }
}
