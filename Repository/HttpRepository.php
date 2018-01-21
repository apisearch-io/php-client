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

namespace Apisearch\Repository;

use Apisearch\Config\Config;
use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpClient;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HttpRepository.
 */
class HttpRepository extends Repository
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
        $response = null;

        if (!empty($itemsToUpdate)) {
            $response = $this
                ->httpClient
                ->get(
                    '/items',
                    'post',
                    Http::getQueryValues($this),
                    [
                        'items' => json_encode(
                            array_map(function (Item $item) {
                                return $item->toArray();
                            }, $itemsToUpdate)
                        ),
                    ]);
        }

        if (!empty($itemsToDelete)) {
            $response = $this
                ->httpClient
                ->get('/items',
                    'delete',
                    Http::getQueryValues($this),
                    [
                        'items' => json_encode(
                            array_map(function (ItemUUID $itemUUID) {
                                return $itemUUID->toArray();
                            }, $itemsToDelete)
                        ),
                    ]);
        }

        if (is_null($response)) {
            return;
        }

        $this->throwTransportableExceptionIfNeeded($response);
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
                    Http::QUERY_FIELD => json_encode($query->toArray()),
                ]
            );

        $this->throwTransportableExceptionIfNeeded($response);

        return Result::createFromArray($response['body']);
    }

    /**
     * Create an index.
     *
     * @throws ResourceExistsException
     */
    public function createIndex()
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'post',
                Http::getQueryValues($this),
                []
            );

        $this->throwTransportableExceptionIfNeeded($response);
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

        $this->throwTransportableExceptionIfNeeded($response);
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

        $this->throwTransportableExceptionIfNeeded($response);
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
                Http::getQueryValues($this),
                []
            );

        if (is_null($response)) {
            return false;
        }

        return Response::HTTP_OK === $response['code'];
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
                    Http::CONFIG_FIELD => json_encode($config->toArray()),
                ]
            );

        if (is_null($response)) {
            return;
        }

        $this->throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Transform transportable http errors to exceptions.
     *
     * @param array $response
     *
     * @throw TransportableException
     */
    private function throwTransportableExceptionIfNeeded(array $response)
    {
        switch ($response['code']) {
            case ResourceNotAvailableException::getTransportableHTTPError():
                throw new ResourceNotAvailableException($response['body']['message']);
            case InvalidFormatException::getTransportableHTTPError():
                throw new InvalidFormatException($response['body']['message']);
        }
    }
}
