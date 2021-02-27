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
 * Class EmptyBodyException.
 */
class PayloadTooLargeException extends TransportableException
{
    /**
     * @return int
     */
    public static function getTransportableHTTPError(): int
    {
        return 413;
    }

    /**
     * @return self
     */
    public static function create(): self
    {
        return new static('You sent us a too large payload. Please, consider reducing this size.');
    }
}
