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
 * Class ConnectionException.
 */
class ConnectionException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 500;
    }

    /**
     * Build new connect exception.
     *
     * @param string $url
     *
     * @return ConnectionException
     */
    public static function buildConnectExceptionByUrl(string $url)
    {
        return new ConnectionException(sprintf('Unable to connect to Apisearch server in %s', $url));
    }
}
