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

use Exception;

/**
 * Class MockException.
 */
class MockException extends Exception
{
    /**
     * This is just a mock.
     *
     * @return MockException
     */
    public static function isAMock(): self
    {
        return new self('You are using a mock');
    }
}
