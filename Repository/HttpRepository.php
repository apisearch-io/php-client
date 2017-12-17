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

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\HttpClient;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;

/**
 * Class HttpRepository.
 */
class HttpRepository extends Repository
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
     * Language query param field
     */
    const LANGUAGE_FIELD = 'language';

    /**
     * @var HttpClient
     *
     * Http client
     */
    private $httpClient;

    /**
     * @var bool
     *
     * Write (Post/Delete) Asynchronous
     */
    private $writeAsync;

    /**
     * HttpAdapter constructor.
     *
     * @param HttpClient $httpClient
     * @param bool       $writeAsync
     */
    public function __construct(
        HttpClient $httpClient,
        bool $writeAsync = false
    ) {
        parent::__construct();

        $this->httpClient = $httpClient;
        $this->writeAsync = $writeAsync;
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
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        if (!empty($itemsToUpdate)) {
            $response = $this
                ->httpClient
                ->get(
                    '/items',
                    'post'.$async,
                    $this->getQueryValues(),
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
                    'delete'.$async,
                    $this->getQueryValues(),
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

        switch ($response['code']) {
            case ResourceNotAvailableException::getTransportableHTTPError():
                throw ResourceNotAvailableException::indexNotAvailable();
            case InvalidFormatException::getTransportableHTTPError():
                throw new InvalidFormatException($response['body']['message']);
        }
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
                $this->getQueryValues(),
                [
                    'query' => json_encode($query->toArray()),
                ]
            );

        switch ($response['code']) {
            case ResourceNotAvailableException::getTransportableHTTPError():
                throw ResourceNotAvailableException::indexNotAvailable();
            case InvalidFormatException::getTransportableHTTPError():
                throw new InvalidFormatException($response['body']['message']);
        }

        return Result::createFromArray($response['body']);
    }

    /**
     * Create an index.
     *
     * @param null|string $language
     *
     * @throws ResourceExistsException
     */
    public function createIndex(? string $language)
    {
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        $response = $this
            ->httpClient
            ->get(
                '/index',
                'post'.$async,
                $this->getQueryValues(),
                [
                    'language' => $language,
                ]
            );

        if ($response['code'] === ResourceExistsException::getTransportableHTTPError()) {
            throw new ResourceExistsException($response['body']['message']);
        }
    }

    /**
     * Delete an index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        $response = $this
            ->httpClient
            ->get(
                '/index',
                'delete'.$async,
                $this->getQueryValues()
            );

        if ($response['code'] === ResourceNotAvailableException::getTransportableHTTPError()) {
            throw new ResourceNotAvailableException($response['body']['message']);
        }
    }

    /**
     * Reset the index.
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex()
    {
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        $response = $this
            ->httpClient
            ->get(
                '/index/reset',
                'post'.$async,
                $this->getQueryValues()
            );

        if ($response['code'] === ResourceNotAvailableException::getTransportableHTTPError()) {
            throw new ResourceNotAvailableException($response['body']['message']);
        }
    }

    /**
     * Get common query values.
     *
     * @return string[]
     */
    private function getQueryValues(): array
    {
        return [
            self::APP_ID_FIELD => $this->getAppId(),
            self::INDEX_FIELD => $this->getIndex(),
            self::TOKEN_FIELD => $this->getToken(),
        ];
    }
}
