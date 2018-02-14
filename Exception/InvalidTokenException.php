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
 * Class InvalidTokenException.
 */
class InvalidTokenException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 401;
    }

    /**
     * Throw an invalid key exception.
     *
     * @param string $tokenReference
     *
     * @return InvalidTokenException
     */
    public static function createInvalidTokenPermissions(string $tokenReference): self
    {
        return new self(sprintf('Token %s not valid', $tokenReference));
    }

    /**
     * Throw an invalid key exception.
     *
     * @param string $tokenReference
     * @param int    $maxHitsPerQuery
     *
     * @return InvalidTokenException
     */
    public static function createInvalidTokenMaxHitsPerQuery(
        string $tokenReference,
        int $maxHitsPerQuery
    ): self {
        return new self(sprintf('Token %s not valid. Max %d hits allowed', $tokenReference, $maxHitsPerQuery));
    }
}
