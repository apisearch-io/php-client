<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Repository;

use Puntmig\Search\Http\HttpClient;
use Puntmig\Search\Model\Item;
use Puntmig\Search\Model\ItemUUID;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

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
        if (!empty($itemsToUpdate)) {
            $this
                ->httpClient
                ->get('/items', 'post', [
                    'key' => $this->getKey(),
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
                ->get('/items', 'delete', [
                    'key' => $this->getKey(),
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
            ->get('/', 'get', [
                'key' => $this->getKey(),
                'query' => json_encode($query->toArray()),
            ]);

        return Result::createFromArray($response['body']);
    }

    /**
     * Reset the index.
     *
     * @var null|string
     */
    public function reset(? string $language)
    {
        $this
            ->httpClient
            ->get('/', 'delete', [
                'key' => $this->getKey(),
                'language' => $language,
            ]);
    }
}
