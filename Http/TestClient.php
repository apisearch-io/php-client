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

namespace Apisearch\Http;

use Symfony\Component\HttpKernel\Client;

/**
 * Class TestClient.
 */
class TestClient implements HttpClient
{
    /**
     * @var Client
     *
     * test client
     */
    private $client;

    /**
     * TestClient constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a response given some parameters.
     * Return an array with the status code and the body.
     *
     * @param string $url
     * @param string $method
     * @param array  $query
     * @param array  $body
     * @param array  $server
     *
     * @return array
     */
    public function get(
        string $url,
        string $method,
        array $query = [],
        array $body = [],
        array $server = []
    ): array {
        $method = trim(str_ireplace('async', '', $method));
        $this
            ->client
            ->request(
                $method,
                $url,
                $parameters,
                [],
                $server
            )
        ;

        $response = $this
            ->client
            ->getResponse();

        return [
            'code' => $response->getStatusCode(),
            'body' => json_decode($response->getContent(), true),
        ];
    }
}
