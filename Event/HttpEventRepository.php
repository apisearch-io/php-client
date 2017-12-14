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
use Apisearch\Http\HttpClient;
use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class HttpEventRepository.
 */
class HttpEventRepository extends RepositoryWithCredentials implements EventRepository
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
     * Create repository.
     *
     * @param bool $removeIfExists
     */
    public function createRepository(bool $removeIfExists = false)
    {
        throw EventException::throwEndpointNotAvailable();
    }

    /**
     * Get all events.
     *
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
     *
     * @return Event[]
     */
    public function all(
        string $name = null,
        ? int $from = null,
        ? int $to = null,
        ? int $length = 10,
        ? int $offset = 0
    ): array {
        $response = $this
            ->httpClient
            ->get('/events/', 'get', [
                'app_id' => $this->getAppId(),
                'index' => $this->getIndex(),
                'token' => $this->getToken(),
                'name' => $name,
                'from' => $from,
                'to' => $to,
                'length' => $length,
                'offset' => $offset,
            ]);

        return array_map(function (array $event) {
            return Event::createFromArray($event);
        }, $response['body']);
    }

    /**
     * Get stats.
     *
     * @param int|null $from
     * @param int|null $to
     *
     * @return Stats
     */
    public function stats(
        ? int $from = null,
        ? int $to = null
    ): Stats {
        $response = $this
            ->httpClient
            ->get('/events/stats', 'get', [
                'app_id' => $this->getAppId(),
                'index' => $this->getIndex(),
                'token' => $this->getToken(),
                'from' => $from,
                'to' => $to,
            ]);

        return Stats::createFromArray($response['body']);
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

    /**
     * Get last event.
     *
     * @return Event|null
     *
     * @throws EventException
     */
    public function last(): ? Event
    {
        throw EventException::throwEndpointNotAvailable();
    }
}
