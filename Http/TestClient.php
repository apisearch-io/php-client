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

use Symfony\Component\BrowserKit\Client as BrowserKitClient;

/**
 * Class TestClient.
 */
class TestClient extends Client implements HttpClient
{
    /**
     * @var BrowserKitClient
     *
     * test client
     */
    private $client;

    /**
     * TestClient constructor.
     *
     * @param BrowserKitClient $client
     * @param string           $version
     * @param RetryMap         $retryMap
     */
    public function __construct(
        BrowserKitClient $client,
        string $version,
        RetryMap $retryMap
    ) {
        $this->client = $client;
        parent::__construct(
            $version,
            $retryMap
        );
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
        $method = trim(strtolower($method));
        $requestParts = $this->buildRequestParts(
            $url,
            $method,
            $query,
            $body,
            $server
        );

        $this
            ->client
            ->request(
                $method,
                '/'.$requestParts->getUrl(),
                $requestParts->getParameters()['form_params'],
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
