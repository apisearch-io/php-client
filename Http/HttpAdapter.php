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

namespace Apisearch\Http;

use Apisearch\Exception\ConnectionException;

/**
 * Interface HttpAdapter.
 */
interface HttpAdapter
{
    /**
     * Get.
     *
     * @param string       $host
     * @param string       $method
     * @param RequestParts $requestParts
     *
     * @return array
     *
     * @throws ConnectionException
     */
    public function getByRequestParts(
        string $host,
        string $method,
        RequestParts $requestParts
    ): array;
}
