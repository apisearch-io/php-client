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

use RuntimeException;

/**
 * Class TransportableException.
 */
abstract class TransportableException extends RuntimeException
{
    /**
     * Get http error code.
     *
     * @return int
     */
    abstract public static function getTransportableHTTPError(): int;
}
