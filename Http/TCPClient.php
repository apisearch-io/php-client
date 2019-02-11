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
use Exception;

/**
 * Class TCPClient.
 */
class TCPClient extends Client implements HttpClient
{
    /**
     * @var string
     *
     * Host
     */
    private $host;

    /**
     * @var HttpAdapter
     *
     * Http Adapter
     */
    private $httpAdapter;

    /**
     * GuzzleClient constructor.
     *
     * @param string      $host
     * @param HttpAdapter $httpAdapter
     * @param string      $version
     * @param RetryMap    $retryMap
     */
    public function __construct(
        string $host,
        HttpAdapter $httpAdapter,
        string $version,
        RetryMap $retryMap
    ) {
        $this->host = $host;
        $this->httpAdapter = $httpAdapter;

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

        return $this->tryRequest(function () use ($method, $requestParts) {
            return $this
                ->httpAdapter
                ->getByRequestParts(
                    $this->host,
                    $method,
                    $requestParts
                );
        }, $this
            ->retryMap
            ->getRetry(
                $url,
                $method
            )
        );
    }

    /**
     * Try connection and return result.
     *
     * Retry n times this connection before returning response.
     *
     * @param callable   $callable
     * @param Retry|null $retry
     *
     * @return array
     *
     * @throws Exception
     */
    private function tryRequest(
        callable $callable,
        ?Retry $retry
    ): array {
        $tries = $retry instanceof Retry
            ? $retry->getRetries()
            : 0;

        while (true) {
            try {
                return $callable();
            } catch (\Exception $e) {
                if ($tries-- <= 0) {
                    throw $e;
                }

                usleep($retry->getMicrosecondsBetweenRetries());
            }
        }
    }
}
