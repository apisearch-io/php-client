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

namespace Apisearch\Repository;

use Apisearch\Http\Http;
use Apisearch\Http\HttpClient;
use Apisearch\Http\HttpResponsesToException;
use Apisearch\Model\Changes;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Class HttpRepository.
 */
class HttpRepository extends Repository
{
    use HttpResponsesToException;

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
        parent::__construct();

        $this->httpClient = $httpClient;
    }

    /**
     * Flush items.
     *
     * @param Item[]     $itemsToUpdate
     * @param ItemUUID[] $itemsToDelete
     */
    protected function flushItems(
        array $itemsToUpdate,
        array $itemsToDelete
    ) {
        if (!empty($itemsToUpdate)) {
            $response = $this
                ->httpClient
                ->get(
                    '/items',
                    'post',
                    Http::getQueryValues($this),
                    [
                        'items' => array_map(function (Item $item) {
                            return $item->toArray();
                        }, $itemsToUpdate),
                    ]);

            self::throwTransportableExceptionIfNeeded($response);
        }

        if (!empty($itemsToDelete)) {
            $response = $this
                ->httpClient
                ->get('/items',
                    'delete',
                    Http::getQueryValues($this),
                    [
                        'items' => array_map(function (ItemUUID $itemUUID) {
                            return $itemUUID->toArray();
                        }, $itemsToDelete),
                    ]);

            self::throwTransportableExceptionIfNeeded($response);
        }
    }

    /**
     * Update items.
     *
     * @param Query   $query
     * @param Changes $changes
     */
    public function updateItems(
        Query $query,
        Changes $changes
    ) {
        $response = $this
            ->httpClient
            ->get(
                '/items',
                'put',
                Http::getQueryValues($this),
                [
                    Http::QUERY_FIELD => $query->toArray(),
                    Http::CHANGES_FIELD => $changes->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Search across the index types.
     *
     * @param Query $query
     * @param array $parameters
     *
     * @return Result
     */
    public function query(
        Query $query,
        array $parameters = []
    ): Result {
        $response = $this
            ->httpClient
            ->get(
                '/',
                'get',
                Http::getQueryValues($this) + [
                    Http::QUERY_FIELD => urlencode(json_encode($query->toArray())),
                ] + $parameters
            );

        self::throwTransportableExceptionIfNeeded($response);

        return Result::createFromArray($response['body']);
    }
}
