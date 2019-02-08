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

namespace Apisearch\Http;

use Apisearch\Exception\ConnectionException;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
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
     * @var GuzzleHttpClient
     *
     * Client
     */
    private $client;

    /**
     * GuzzleClient constructor.
     *
     * @param GuzzleHttpClient $client
     * @param string           $host
     * @param string           $version
     * @param RetryMap         $retryMap
     */
    public function __construct(
        GuzzleHttpClient $client,
        string $host,
        string $version,
        RetryMap $retryMap
    ) {
        $this->client = $client;
        $this->host = $host;

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
     *
     * @throws ConnectionException
     */
    public function get(
        string $url,
        string $method,
        array $query = [],
        array $body = [],
        array $server = []
    ): array {
        $method = strtolower($method);
        $requestParts = $this->buildRequestParts(
            $url,
            $query,
            $body,
            $server
        );

        /**
         * @var ResponseInterface
         */
        $response = $this->tryRequest($url, function () use ($method, $requestParts) {
            return $this
                ->client
                ->$method(
                    rtrim($this->host, '/').'/'.ltrim($requestParts->getUrl(), '/'),
                    $requestParts->getParameters() + ['http_errors' => false],
                    $requestParts->getOptions()
                );
        }, $this
            ->retryMap
            ->getRetry(
                $url,
                $method
            )
        );

        return [
            'code' => $response->getStatusCode(),
            'body' => json_decode($response->getBody()->getContents(), true),
        ];
    }

    /**
     * Try connection and return result.
     *
     * Retry n times this connection before returning response.
     *
     * @param string     $url
     * @param callable   $callable
     * @param Retry|null $retry
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    private function tryRequest(
        string $url,
        callable $callable,
        ?Retry $retry
    ): ResponseInterface {
        $tries = $retry instanceof Retry
            ? $retry->getRetries()
            : 0;

        while (true) {
            try {
                return $callable();
            } catch (\Exception $e) {
                if ($tries-- <= 0) {
                    if ($e instanceof GuzzleConnectException) {
                        throw ConnectionException::buildConnectExceptionByUrl($url);
                    }

                    throw $e;
                }

                usleep($retry->getMicrosecondsBetweenRetries());
            }
        }
    }
}
