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
 * Class ResourceExistsException.
 */
class ResourceExistsException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 409;
    }

    /**
     * Index exists.
     *
     * @return ResourceExistsException
     */
    public static function indexExists(): self
    {
        return new self('Index exists and cannot be created again');
    }
}
