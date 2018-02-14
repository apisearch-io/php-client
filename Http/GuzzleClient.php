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

use GuzzleHttp\Client as GuzzleHttpClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleClient.
 */
class GuzzleClient extends Client implements HttpClient
{
    /**
     * @var string
     *
     * Host
     */
    private $host;

    /**
     * GuzzleClient constructor.
     *
     * @param string $host
     * @param string $version
     */
    public function __construct(
        string $host,
        string $version
    ) {
        $this->host = $host;
        parent::__construct($version);
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
        $method = strtolower($method);
        $client = new GuzzleHttpClient([
            'defaults' => [
                'timeout' => 5,
            ],
        ]);

        $requestParts = $this->buildRequestParts(
            $url,
            $method,
            $query,
            $body,
            $server
        );

        /**
         * @var ResponseInterface
         */
        $response = $client->$method(
            rtrim($this->host, '/').'/'.ltrim($requestParts->getUrl(), '/'),
            $requestParts->getParameters() + ['http_errors' => false],
            $requestParts->getOptions()
        );

        return [
            'code' => $response->getStatusCode(),
            'body' => json_decode($response->getBody()->getContents(), true),
        ];
    }
}
