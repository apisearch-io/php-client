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
     * @param mixed $itemBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function itemRepresentationNotValid($itemBeforeHydration): self
    {
        return new static('Items representation not valid. Expecting Item array serialized but found malformed data');
    }

    /**
     * Items UUID representation format not valid.
     *
     * @param mixed $itemUUIDBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function itemUUIDRepresentationNotValid($itemUUIDBeforeHydration): self
    {
        return new static('Item UUID representation not valid. Expecting UUID array serialized but found malformed data');
    }

    /**
     * Create Composed UUID bad format.
     *
     * @param string $composedUUID
     *
     * @return InvalidFormatException
     */
    public static function composedItemUUIDNotValid($composedUUID): self
    {
        return new static('A composed UUID should always follow this format: {id}~{type}.');
    }

    /**
     * Create Query sorted by distance without coordinate.
     *
     * @return InvalidFormatException
     */
    public static function querySortedByDistanceWithoutCoordinate(): self
    {
        return new static('In order to be able to sort by coordinates, you need to create a Query by using Query::createLocated() instead of Query::create()');
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
        return new static('Query Format not valid. Expecting a Query serialized but found malformed data');
    }

    /**
     * Coordinate format not valid.
     *
     * @return InvalidFormatException
     */
    public static function coordinateFormatNotValid(): self
    {
        return new static('A Coordinate should always contain a lat (Latitude) and a lon (Longitude)');
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
        return new static('Config Format not valid. Expecting a Config serialized but found malformed data');
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
        return new static('Token Format not valid. Expecting a Token serialized but found malformed data');
    }

    /**
     * Index format not valid.
     *
     * @return InvalidFormatException
     */
    public static function indexFormatNotValid(): self
    {
        return new static('Index Format not valid. Expecting an Index serialized but found malformed data');
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
        return new static('Campaign Format not valid. Expecting a Campaign serialized but found malformed data');
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
        return new static('Changes Format not valid. Expecting a Changes serialized but found malformed data');
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
        return new static('Boost clause Format not valid. Expecting a Boost clause serialized but found malformed data');
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
        return new static('Token UUID Format not valid. Expecting a TokenUUID serialized but found malformed data');
    }

    /**
     * User format not valid.
     *
     * @param mixed $userBeforeHydration
     *
     * @return InvalidFormatException
     */
    public static function userFormatNotValid($userBeforeHydration): self
    {
        return new static('User Format not valid. Expecting a User serialized but found malformed data');
    }
}
