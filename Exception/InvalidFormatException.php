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
        return new static(sprintf('Items representation not valid. Expecting Item array serialized but found malformed data'));
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
        return new static(sprintf('Items UUID representation not valid. Expecting UUID array serialized but found malformed data'));
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
        return new static(sprintf('Query Format not valid. Expecting a Query serialized but found malformed data'));
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
        return new static(sprintf('Config Format not valid. Expecting a Config serialized but found malformed data'));
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
        return new static(sprintf('Token Format not valid. Expecting a Token serialized but found malformed data'));
    }

    /**
     * Campaign format not valid.
     *
     * @param mixed $campaignBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function campaignFormatNotValid($campaignBeforeHydration): self
    {
        return new static(sprintf('Campaign Format not valid. Expecting a Campaign serialized but found malformed data'));
    }

    /**
     * Changes format not valid.
     *
     * @param mixed $changesBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function changesFormatNotValid($changesBeforeHydration): self
    {
        return new static(sprintf('Changes Format not valid. Expecting a Changes serialized but found malformed data'));
    }

    /**
     * Boost clause format not valid.
     *
     * @param mixed $boostClauseBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function boostClauseFormatNotValid($boostClauseBeforeHydration): self
    {
        return new static(sprintf('Boost clause Format not valid. Expecting a Boost clause serialized but found malformed data'));
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
        return new static(sprintf('Token UUID Format not valid. Expecting a TokenUUID serialized but found malformed data'));
    }
}
