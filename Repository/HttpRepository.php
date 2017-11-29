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
     * Key query param field
     */
    const KEY_FIELD = 'key';

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
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        if (!empty($itemsToUpdate)) {
            $this
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
            $this
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
    }

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
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

        return Result::createFromArray($response['body']);
    }

    /**
     * Reset the index.
     *
     * @var null|string
     */
    public function reset(? string $language)
    {
        $async = ($this->writeAsync)
            ? 'Async'
            : ''
        ;

        $this
            ->httpClient
            ->get(
                '/',
                'post'.$async,
                $this->getQueryValues(),
                [
                    'language' => $language,
                ]
            );
    }

    /**
     * Get common query values.
     *
     * @return string[]
     */
    private function getQueryValues(): array
    {
        return [
            'app_id' => $this->getAppId(),
            'key' => $this->getKey(),
        ];
    }
}
