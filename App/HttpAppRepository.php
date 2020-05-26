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
    public function putToken(Token $token)
    {
        $response = $this
            ->httpClient
            ->get(
                sprintf('/%s/tokens/%s', $this->getAppUUID()->getId(), $token->getTokenUUID()->composeUUID()),
                'put',
                [],
                $token->toArray(),
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/tokens/%s',
                    $this->getAppUUID()->composeUUID(),
                    $tokenUUID->composeUUID()
                ),
                'delete',
                [],
                [],
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/tokens',
                    $this->getAppUUID()->getId()
                ),
                'get',
                [],
                [],
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/tokens',
                    $this->getAppUUID()->getId()
                ),
                'delete',
                [],
                [],
                Http::getApisearchHeaders($this)
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
        $response = $this
            ->httpClient
            ->get(
                sprintf(
                    '/%s/indices',
                    $this->getAppUUID()->getId()
                ),
                'get',
                [],
                [],
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/indices/%s',
                    $this->getAppUUID()->getId(),
                    $indexUUID->composeUUID()
                ),
                'put',
                [],
                $config->toArray(),
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/indices/%s',
                    $this->getAppUUID()->composeUUID(),
                    $indexUUID->composeUUID()
                ),
                'delete',
                [],
                [],
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/indices/%s/reset',
                    $this->getAppUUID()->composeUUID(),
                    $indexUUID->composeUUID()
                ),
                'post',
                [],
                [],
                Http::getApisearchHeaders($this)
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
                sprintf(
                    '/%s/indices/%s',
                    $this->getAppUUID()->composeUUID(),
                    $indexUUID->composeUUID()
                ),
                'head',
                [],
                [],
                Http::getApisearchHeaders($this)
            );

        if (null === $response) {
            return false;
        }

        return 200 === $response['code'];
    }

    /**
     * Config the index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     * @param bool $forceReindex
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        IndexUUID $indexUUID,
        Config $config,
        bool $forceReindex = false
    ) {
        $response = $this
            ->httpClient
            ->get(
                sprintf(
                    '/%s/indices/%s/configure',
                    $this->getAppUUID()->composeUUID(),
                    $indexUUID->composeUUID()
                ),
                'post',
                [
                    'force_reindex' => $forceReindex
                ],
                $config->toArray(),
                Http::getApisearchHeaders($this)
            );

        if (null === $response) {
            return;
        }

        self::throwTransportableExceptionIfNeeded($response);
    }
}
