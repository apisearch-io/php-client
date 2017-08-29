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

namespace Puntmig\Search\Http;

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
     * GuzzleClient constructor.
     *
     * @param string $host
     */
    public function __construct(string $host)
    {
        $this->host = $host;
    }

    /**
     * Get a response given some parameters.
     * Return an array with the status code and the body.
     *
     * @param string $url
     * @param string $method
     * @param array  $parameters
     * @param array  $server
     *
     * @return array
     */
    public function get(
        string $url,
        string $method,
        array $parameters,
        array $server = []
    ): array {
        $client = new Client([
            'defaults' => [
                'timeout' => 5,
            ],
        ]);

        $bodyFieldName = ($method === 'get')
            ? 'query'
            : 'form_params';

        /**
         * @var ResponseInterface|Promise
         */
        $response = $client->$method(
            rtrim($this->host.$url, '/'),
            [
                $bodyFieldName => $parameters,
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
