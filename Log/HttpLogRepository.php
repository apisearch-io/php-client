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

namespace Apisearch\Log;

use Apisearch\Exception\EventException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpClient;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Result\Logs;

/**
 * Class HttpLogRepository.
 */
class HttpLogRepository extends RepositoryWithCredentials implements LogRepository
{
    /**
     * @var HttpClient
     *
     * Http client
     */
    private $httpClient;

    /**
     * HttpAdapter constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create index.
     *
     * @throws EventException
     * @throws ResourceExistsException
     */
    public function createIndex()
    {
        $response = $this
            ->httpClient
            ->get('/logs', 'post', [
                'app_id' => $this->getAppId(),
                'index' => $this->getIndex(),
                'token' => $this->getToken(),
            ]);

        if ($response['code'] === ResourceExistsException::getTransportableHTTPError()) {
            throw new ResourceExistsException($response['body']['message']);
        }
    }

    /**
     * Delete index.
     *
     * @throws EventException
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        $response = $this
            ->httpClient
            ->get('/logs', 'delete', [
                'app_id' => $this->getAppId(),
                'index' => $this->getIndex(),
                'token' => $this->getToken(),
            ]);

        if ($response['code'] === ResourceNotAvailableException::getTransportableHTTPError()) {
            throw new ResourceNotAvailableException($response['body']['message']);
        }
    }

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
                    Http::QUERY_FIELD => json_encode($query->toArray()),
                    Http::FROM_FIELD => $from,
                    Http::TO_FIELD => $to,
                ]
            );

        if ($response['code'] === ResourceNotAvailableException::getTransportableHTTPError()) {
            throw new ResourceNotAvailableException($response['body']['message']);
        }

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
