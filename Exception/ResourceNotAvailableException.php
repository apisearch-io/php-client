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
        return 404;
    }

    /**
     * Index is not available.
     *
     * @param string $resourceId
     *
     * @return ResourceNotAvailableException
     */
    public static function indexNotAvailable(string $resourceId): self
    {
        return new self(sprintf('Index not available - %s', $resourceId));
    }

    /**
     * Events index is not available.
     *
     * @param string $resourceId
     *
     * @return ResourceNotAvailableException
     */
    public static function eventsIndexNotAvailable(string $resourceId): self
    {
        return new self(sprintf('Events index not available - %s', $resourceId));
    }

    /**
     * Logs index is not available.
     *
     * @param string $resourceId
     *
     * @return ResourceNotAvailableException
     */
    public static function logsIndexNotAvailable(string $resourceId): self
    {
        return new self(sprintf('Logs index not available - %s', $resourceId));
    }

    /**
     * Engine is not available.
     *
     * @param string $resourceId
     *
     * @return ResourceNotAvailableException
     */
    public static function engineNotAvailable(string $resourceId): self
    {
        return new self(sprintf('Engine not available - %s', $resourceId));
    }
}
