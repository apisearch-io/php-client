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

use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpClient;
use Apisearch\Http\HttpResponsesToException;
use Apisearch\Model\Changes;
use Apisearch\Model\Index;
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
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    public function query(Query $query): Result
    {
        $response = $this
            ->httpClient
            ->get(
                '/',
                'get',
                Http::getQueryValues($this),
                [
                    Http::QUERY_FIELD => $query->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);

        return Result::createFromArray($response['body']);
    }

    /**
     * @param string|null $appId
     *
     * @return array|Index[]
     */
    public function getIndices(string $appId = null): array
    {
        $queryParams = Http::getQueryValues($this);
        if (!empty($appId)) {
            $queryParams['app-id'] = $appId;
        }

        $response = $this
            ->httpClient
            ->get(
                '/indices',
                'get',
                $queryParams
            );

        self::throwTransportableExceptionIfNeeded($response);

        $result = [];
        foreach ($response['body'] as $index) {
            $result[] = Index::createFromArray($index);
        }

        return $result;
    }

    /**
     * Create an index.
     *
     * @param ImmutableConfig $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(ImmutableConfig $config)
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'post',
                Http::getQueryValues($this),
                [
                    Http::CONFIG_FIELD => $config->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Delete an index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'delete',
                Http::getQueryValues($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Reset the index.
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex()
    {
        $response = $this
            ->httpClient
            ->get(
                '/index/reset',
                'post',
                Http::getQueryValues($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Checks the index.
     *
     * @return bool
     */
    public function checkIndex(): bool
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'head',
                Http::getQueryValues($this)
            );

        if (is_null($response)) {
            return false;
        }

        return 200 === $response['code'];
    }

    /**
     * Config the index.
     *
     * @param Config $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(Config $config)
    {
        $response = $this
            ->httpClient
            ->get(
                '/index/config',
                'post',
                Http::getQueryValues($this),
                [
                    Http::CONFIG_FIELD => $config->toArray(),
                ]
            );

        if (is_null($response)) {
            return;
        }

        self::throwTransportableExceptionIfNeeded($response);
    }
}
