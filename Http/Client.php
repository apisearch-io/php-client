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
     * Client constructor.
     *
     * @param string $version
     */
    public function __construct(string $version)
    {
        $this->version = trim($version, '/');
    }

    /**
     * Get some parameters and build a RequestParts instance.
     *
     * @param string $url
     * @param string $method
     * @param array  $query
     * @param array  $body
     * @param array  $server
     *
     * @return RequestParts
     */
    public function buildRequestParts(
        string $url,
        string $method,
        array $query = [],
        array $body = [],
        array $server = []
    ): RequestParts {
        $url = trim($url, '/');
        $url = trim("{$this->version}/$url", '/');

        /*
         * If method is GET, then we merge both the query and the body
         * parameters. Otherwise, the query params will be appended into the url
         * and the body will be served as the body itself
         */
        if ('get' !== $method) {
            $url = $this->buildUrlParams($url, $query);
        } else {
            $url = $this->buildUrlParams($url, array_merge(
                $query,
                $body
            ));
            $body = [];
        }

        return new RequestParts(
            $url,
            [
                'form_params' => $body,
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
