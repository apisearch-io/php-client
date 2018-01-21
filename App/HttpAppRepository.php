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

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\Http;
use Apisearch\Http\HttpClient;
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class HttpAppRepository.
 */
class HttpAppRepository extends RepositoryWithCredentials implements AppRepository
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
        $this->httpClient = $httpClient;
    }

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
                    'token' => json_encode($token->toArray()),
                ]);

        $this->throwTransportableExceptionIfNeeded($response);
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
                    'token' => json_encode($tokenUUID->toArray()),
                ]);

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
