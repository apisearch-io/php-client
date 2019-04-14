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
    /**
     * @var string
     *
     * Index query param field
     */
    const INDEX_FIELD = 'index';

    /**
     * @var string
     *
     * Token query param field
     */
    const TOKEN_FIELD = 'token';

    /**
     * @var string
     *
     * Changes query param field
     */
    const CHANGES_FIELD = 'changes';

    /**
     * @var string
     *
     * Query query param field
     */
    const QUERY_FIELD = 'query';

    /**
     * @var string
     *
     * Config param field
     */
    const CONFIG_FIELD = 'config';

    /**
     * @var string
     *
     * Language query param field
     */
    const LANGUAGE_FIELD = 'language';

    /**
     * @var string
     *
     * From field
     */
    const FROM_FIELD = 'from';

    /**
     * @var string
     *
     * From field
     */
    const TO_FIELD = 'to';

    /**
     * @var string
     *
     * App ID Header
     */
    const APP_ID_HEADER = 'APISEARCH-APP-ID';

    /**
     * @var string
     *
     * Token ID Header
     */
    const TOKEN_ID_HEADER = 'APISEARCH-TOKEN-ID';

    /**
     * Get common query values.
     *
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
