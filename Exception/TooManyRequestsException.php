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
 * Class TooManyRequestsException.
 */
class TooManyRequestsException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 429;
    }

    /**
     * Index exists.
     *
     * @return ResourceExistsException
     */
    public static function tooManyRequestsReached(): self
    {
        return new self('You reached the rate limit. Please, check your permissions');
    }
}
