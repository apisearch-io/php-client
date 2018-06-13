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

namespace Apisearch\Event;

use Apisearch\Exception\EventException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Query\Query;
use Apisearch\Result\Events;

/**
 * Class HttpEventRepository.
 */
class HttpEventRepository extends HttpRepositoryWithCredentials implements EventRepository
{
    /**
     * Query over events.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Events
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Events {
        $response = $this
            ->httpClient
            ->get(
                '/events',
                'get',
                Http::getQueryValues($this),
                [
                    Http::QUERY_FIELD => $query->toArray(),
                    Http::FROM_FIELD => $from,
                    Http::TO_FIELD => $to,
                ]
            );

        $this->throwTransportableExceptionIfNeeded($response);

        return Events::createFromArray($response['body']);
    }

    /**
     * Save event.
     *
     * @param Event $event
     *
     * @throws EventException
     */
    public function save(Event $event)
    {
        throw EventException::throwEndpointNotAvailable();
    }
}
