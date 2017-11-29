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

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleClient.
 */
class GuzzleClient implements HttpClient
{
    /**
     * @var string
     *
     * Host
     */
    private $host;

    /**
     * @var string
     *
     * Version
     */
    private $version;

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
        $this->version = trim($version, '/');
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
        $client = new Client([
            'defaults' => [
                'timeout' => 5,
            ],
        ]);

        $url = trim($url, '/');
        $url = trim("{$this->host}/{$this->version}/$url", '/');

        $bodyFieldName = ($method === 'get')
            ? 'query'
            : 'form_params';

        /*
         * If method is GET, then we merge both the query and the body
         * parameters. Otherwise, the query params will be appended into the url
         * and the body will be served as the body itself
         */
        if ($method !== 'get') {
            array_walk($query, function (&$value, $key) {
                $value = "$key=$value";
            });

            $url .= '?'.implode('&', $query);
        } else {
            $body = array_merge(
                $query,
                $body
            );
        }

        /**
         * @var ResponseInterface|Promise
         */
        $response = $client->$method(
            $url,
            [
                $bodyFieldName => $body,
                'headers' => $server,
            ],
            [
                'decode_content' => 'gzip',
            ]
        );

        return ($response instanceof Response)
            ? [
                'code' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true),
            ]
            : [
                'code' => 200,
                'body' => [
                    'message' => 'Task enqueued successfully',
                ],
            ]
        ;
    }
}
