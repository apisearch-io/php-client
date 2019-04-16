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

namespace Apisearch\User;

use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;

/**
 * Class HttpUserRepository.
 */
class HttpUserRepository extends HttpRepositoryWithCredentials implements UserRepository
{
    /**
     * Add interaction.
     *
     * @param Interaction $interaction
     */
    public function addInteraction(Interaction $interaction)
    {
        $response = $this
            ->httpClient
            ->get(
                sprintf(
                    '/%s/interactions',
                    $this->getAppUUID()->composeUUID()
                ),
                'post',
                [],
                $interaction->toArray(),
                Http::getApisearchHeaders($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }
}
