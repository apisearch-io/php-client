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
                    sprintf(
                        '/%s/indices/%s/items',
                        $this->getAppUUID()->composeUUID(),
                        $this->getIndexUUID()->composeUUID()
                    ),
                    'put',
                    [],
                    array_map(function (Item $item) {
                        return $item->toArray();
                    }, $itemsToUpdate),
                    Http::getApisearchHeaders($this)
                );

            self::throwTransportableExceptionIfNeeded($response);
        }

        if (!empty($itemsToDelete)) {
            $response = $this
                ->httpClient
                ->get(
                    sprintf(
                        '/%s/indices/%s/items',
                        $this->getAppUUID()->composeUUID(),
                        $this->getIndexUUID()->composeUUID()
                    ),
                    'delete',
                    [],
                    array_map(function (ItemUUID $itemUUID) {
                        return $itemUUID->toArray();
                    }, $itemsToDelete),
                    Http::getApisearchHeaders($this)
                );

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
                sprintf(
                    '/%s/indices/%s/items/update-by-query',
                    $this->getAppUUID()->composeUUID(),
                    $this->getIndexUUID()->composeUUID()
                ),
                'post',
                [],
                [
                    Http::QUERY_FIELD => $query->toArray(),
                    Http::CHANGES_FIELD => $changes->toArray(),
                ],
                Http::getApisearchHeaders($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Delete items by query.
     *
     * @param Query $query
     */
    public function deleteItemsByQuery(Query $query)
    {
        $response = $this
            ->httpClient
            ->get(
                sprintf(
                    '/%s/indices/%s/items/by-query',
                    $this->getAppUUID()->composeUUID(),
                    $this->getIndexUUID()->composeUUID()
                ),
                'delete',
                [],
                $query->toArray(),
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/indices/%s',
                    $this->getAppUUID()->composeUUID(),
                    $this->getIndexUUID()->composeUUID()
                ),
                'get',
                [
                    Http::QUERY_FIELD => urlencode(json_encode($query->toArray())),
                ] + $parameters,
                [],
                Http::getApisearchHeaders($this)
            );

        self::throwTransportableExceptionIfNeeded($response);

        return Result::createFromArray($response['body']);
    }
}
