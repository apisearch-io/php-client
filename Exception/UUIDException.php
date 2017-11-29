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
 * Class UUIDException.
 */
class UUIDException extends ModelException
{
    /**
     * Create UUID bad format exception.
     *
     * @return UUIDException
     */
    public static function createUUIDBadFormatException(): UUIDException
    {
        return new self('An Item should always contain a UUID, with an ID and a Type');
    }

    /**
     * Create Composed UUID bad format.
     *
     * @param string $composedUUID
     *
     * @return UUIDException
     */
    public static function createComposedUUIDBadFormatException(string $composedUUID): UUIDException
    {
        return new self(sprintf('A composed UUID should always follow this format: {type}~{id}. %s given', $composedUUID));
    }
}
