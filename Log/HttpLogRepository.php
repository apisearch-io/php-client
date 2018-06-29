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

namespace Apisearch\Log;

use Apisearch\Exception\EventException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Query\Query;
use Apisearch\Result\Logs;

/**
 * Class HttpLogRepository.
 */
class HttpLogRepository extends HttpRepositoryWithCredentials implements LogRepository
{
    /**
     * Query over events.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Logs
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Logs {
        $response = $this
            ->httpClient
            ->get(
                '/logs',
                'get',
                Http::getQueryValues($this),
                [
                    Http::QUERY_FIELD => $query->toArray(),
                    Http::FROM_FIELD => $from,
                    Http::TO_FIELD => $to,
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);

        return Logs::createFromArray($response['body']);
    }

    /**
     * Save log.
     *
     * @param Log $log
     *
     * @throws EventException
     */
    public function save(Log $log)
    {
        throw EventException::throwEndpointNotAvailable();
    }
}
