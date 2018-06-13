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
 * Class ForbiddenException.
 */
class ForbiddenException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 403;
    }

    /**
     * Create app Id should be defined.
     *
     * @return ForbiddenException
     */
    public static function createAppIdIsRequiredException(): self
    {
        return new self('AppId query parameter MUST be defined with a valid value');
    }

    /**
     * Create app Id should be defined.
     *
     * @return ForbiddenException
     */
    public static function createIndexIsRequiredException(): self
    {
        return new self('Index query parameter MUST be defined with a valid value');
    }

    /**
     * Create app Id should be defined.
     *
     * @return ForbiddenException
     */
    public static function createTokenIsRequiredException(): self
    {
        return new self('Index query parameter MUST be defined with a valid value');
    }
}
