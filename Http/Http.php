<?php
/**
 * File header placeholder
 */

namespace Apisearch\Http;

use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class Http
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
     * Purge Query object from response
     */
    const PURGE_QUERY_FROM_RESPONSE_FIELD = 'incl_query';

    /**
     * Get common query values.
     *
     * @return string[]
     */
    public static function getQueryValues(RepositoryWithCredentials $repository): array
    {
        return [
            self::APP_ID_FIELD => $repository->getAppId(),
            self::INDEX_FIELD => $repository->getIndex(),
            self::TOKEN_FIELD => $repository->getToken(),
        ];
    }
}