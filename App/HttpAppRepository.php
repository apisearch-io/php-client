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

namespace Apisearch\App;

use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

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
                Http::getQueryValues($this),
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
                Http::getQueryValues($this),
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
                Http::getQueryValues($this)
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
                Http::getQueryValues($this)
            );

        self::throwTransportableExceptionIfNeeded($response);
    }
}
