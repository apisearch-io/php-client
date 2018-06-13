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
 * Class UnsupportedContentTypeException.
 */
class UnsupportedContentTypeException extends TransportableException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 415;
    }

    /**
     * Create unsupported content type exception.
     *
     * @return UnsupportedContentTypeException
     */
    public static function createUnsupportedContentTypeException(): self
    {
        return new self('This content type is not accepted. Please use application/json');
    }
}
