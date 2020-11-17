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

use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class Http.
 */
class Http
{
    const TOKEN_FIELD = 'token';
    const CHANGES_FIELD = 'changes';
    const QUERY_FIELD = 'query';

    const APP_ID_HEADER = 'APISEARCH-APP-ID';
    const TOKEN_ID_HEADER = 'APISEARCH-TOKEN-ID';

    /**
     * @param RepositoryWithCredentials $repository
     *
     * @return string[]
     */
    public static function getApisearchHeaders(RepositoryWithCredentials $repository): array
    {
        return [
            self::APP_ID_HEADER => $repository->getAppUUID()->composeUUID(),
            self::TOKEN_ID_HEADER => $repository->getTokenUUID()->composeUUID(),
        ];
    }
}
