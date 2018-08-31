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

use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class Http.
 */
class Http
{
    /**
     * @var string
     *
     * App_id query param field
     */
    const APP_ID_FIELD = 'app_id';

    /**
     * @var string
     *
     * App_id query param field
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
     * Items query param field
     */
    const ITEMS_FIELD = 'items';

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
     * Interaction param field
     */
    const INTERACTION_FIELD = 'interaction';

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
     * Purge Query object from response
     */
    const PURGE_QUERY_FROM_RESPONSE_FIELD = 'incl_query';

    /**
     * Get common query values.
     *
     * @param RepositoryWithCredentials $repository
     *
     * @return string[]
     */
    public static function getQueryValues(RepositoryWithCredentials $repository): array
    {
        return [
            self::APP_ID_FIELD => $repository->getAppUUID()->composeUUID(),
            self::INDEX_FIELD => $repository->getIndexUUID()->composeUUID(),
            self::TOKEN_FIELD => $repository->getTokenUUID()->composeUUID(),
        ];
    }

    /**
     * Get common query values.
     *
     * @param RepositoryWithCredentials $repository
     * @param IndexUUID                 $indexUUID
     *
     * @return string[]
     */
    public static function getAppQueryValues(
        RepositoryWithCredentials $repository,
        IndexUUID $indexUUID = null
    ): array {
        return array_filter([
            self::APP_ID_FIELD => $repository->getAppUUID()->composeUUID(),
            self::INDEX_FIELD => $indexUUID instanceof IndexUUID
                ? $indexUUID->composeUUID()
                : false,
            self::TOKEN_FIELD => $repository->getTokenUUID()->composeUUID(),
        ]);
    }
}
