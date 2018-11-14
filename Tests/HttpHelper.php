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

namespace Apisearch\Tests;

use Apisearch\Model\HttpTransportable;

/**
 * Class HttpHelper.
 */
class HttpHelper
{
    /**
     * Emulate http transport transformation.
     *
     * @param HttpTransportable $httpTransportable
     */
    public static function emulateHttpTransport(HttpTransportable $httpTransportable)
    {
        $class = get_class($httpTransportable);

        return $class::createFromArray(
            json_decode(
                json_encode(
                    $httpTransportable->toArray()
                ), true)
        );
    }
}
