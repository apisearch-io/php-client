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
 * Class ResourceNotAvailableException.
 */
class ResourceNotAvailableException extends TransportableException
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
     * Index is not available.
     *
     * @param string $message
     *
     * @return ResourceNotAvailableException
     */
    public static function indexNotAvailable(string $message): self
    {
        return new self(sprintf('Index not available - %s', $message));
    }

    /**
     * Events index is not available.
     *
     * @param string $message
     *
     * @return ResourceNotAvailableException
     */
    public static function eventsIndexNotAvailable(string $message): self
    {
        return new self(sprintf('Events index not available - %s', $message));
    }

    /**
     * Logs index is not available.
     *
     * @param string $message
     *
     * @return ResourceNotAvailableException
     */
    public static function logsIndexNotAvailable(string $message): self
    {
        return new self(sprintf('Logs index not available - %s', $message));
    }
}
