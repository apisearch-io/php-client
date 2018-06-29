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

/**
 * Class Client.
 */
abstract class Client
{
    /**
     * @var string
     *
     * Version
     */
    protected $version;

    /**
     * @var RetryMap
     *
     * Retry map
     */
    protected $retryMap;

    /**
     * Client constructor.
     *
     * @param string   $version
     * @param RetryMap $retryMap
     */
    public function __construct(
        string $version,
        RetryMap $retryMap
    ) {
        $this->version = trim($version, '/');
        $this->retryMap = $retryMap;
    }

    /**
     * Get some parameters and build a RequestParts instance.
     *
     * @param string $url
     * @param array  $query
     * @param array  $body
     * @param array  $server
     *
     * @return RequestParts
     */
    public function buildRequestParts(
        string $url,
        array $query = [],
        array $body = [],
        array $server = []
    ): RequestParts {
        $url = trim($url, '/');
        $url = trim("{$this->version}/$url", '/');
        $url = $this->buildUrlParams($url, $query);

        return new RequestParts(
            $url,
            [
                'json' => $body,
                'headers' => $server,
            ],
            [
                'decode_content' => 'gzip',
            ]
        );
    }

    /**
     * Build url params.
     *
     * @param string   $url
     * @param string[] $params
     *
     * @return string
     */
    private function buildUrlParams(
        string $url,
        array $params
    ) {
        array_walk($params, function (&$value, $key) {
            $value = "$key=$value";
        });

        return $url.'?'.implode('&', $params);
    }
}
