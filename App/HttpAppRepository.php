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

namespace Apisearch\App;

use Apisearch\Config\Config;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;

/**
 * Class HttpAppRepository.
 */
class HttpAppRepository extends HttpRepositoryWithCredentials implements AppRepository
{
    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $response = $this
            ->httpClient
            ->get(
                '/token',
                'post',
                Http::getAppQueryValues($this),
                [
                    Http::TOKEN_FIELD => $token->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        $response = $this
            ->httpClient
            ->get(
                '/token',
                'delete',
                Http::getAppQueryValues($this),
                [
                    Http::TOKEN_FIELD => $tokenUUID->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        $response = $this
            ->httpClient
            ->get(
                '/tokens',
                'get',
                Http::getAppQueryValues($this)
            );

        self::throwTransportableExceptionIfNeeded($response);

        return array_map(function (array $token) {
            return Token::createFromArray($token);
        }, $response['body']);
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        $response = $this
            ->httpClient
            ->get(
                '/tokens',
                'delete',
                Http::getAppQueryValues($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Get indices.
     *
     * @return Index[]
     */
    public function getIndices(): array
    {
        if (!empty($appId)) {
            $queryParams['app-id'] = $appId;
        }

        $response = $this
            ->httpClient
            ->get(
                '/indices',
                'get',
                Http::getAppQueryValues($this)
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
     * @param IndexUUID $indexUUID
     * @param Config    $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        IndexUUID $indexUUID,
        Config $config
    ) {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'put',
                Http::getAppQueryValues($this),
                [
                    Http::INDEX_FIELD => $indexUUID->toArray(),
                    Http::CONFIG_FIELD => $config->toArray(),
                ]
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Delete an index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex(IndexUUID $indexUUID)
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'delete',
                Http::getAppQueryValues($this, $indexUUID)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Reset the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex(IndexUUID $indexUUID)
    {
        $response = $this
            ->httpClient
            ->get(
                '/index/reset',
                'post',
                Http::getAppQueryValues($this, $indexUUID)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Checks the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @return bool
     */
    public function checkIndex(IndexUUID $indexUUID): bool
    {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'head',
                Http::getAppQueryValues($this, $indexUUID)
            );

        if (is_null($response)) {
            return false;
        }

        return 200 === $response['code'];
    }

    /**
     * Config the index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        IndexUUID $indexUUID,
        Config $config
    ) {
        $response = $this
            ->httpClient
            ->get(
                '/index',
                'post',
                Http::getAppQueryValues($this, $indexUUID),
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
