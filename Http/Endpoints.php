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
 * Class Endpoints.
 */
class Endpoints
{
    /**
     * Get all endpoints.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            /*
             * Admin endpoints
             */
            'v1-index-create' => [
                'name' => 'Index create',
                'description' => 'Reset your App index',
                'path' => '/v1/index',
                'verb' => 'post',
            ],
            'v1-index-delete' => [
                'name' => 'Index delete',
                'description' => 'Delete your App index',
                'path' => '/v1/index',
                'verb' => 'delete',
            ],
            'v1-index-reset' => [
                'name' => 'Index reset',
                'description' => 'Reset your App index',
                'path' => '/v1/index/reset',
                'verb' => 'post',
            ],

            /*
             * Write endpoints
             */
            'v1-items-index' => [
                'name' => 'Items index',
                'description' => 'Index your items',
                'path' => '/v1/items',
                'verb' => 'post',
            ],
            'v1-items-delete' => [
                'name' => 'Items delete',
                'description' => 'Delete your items',
                'path' => '/v1/items',
                'verb' => 'delete',
            ],

            /*
             * Read endpoints
             */
            'v1-query' => [
                'name' => 'Query',
                'description' => 'Make queries',
                'path' => '/v1',
                'verb' => 'get',
            ],
            'v1-events' => [
                'name' => 'Events',
                'description' => 'Query your events',
                'path' => '/v1/events',
                'verb' => 'get',
            ],
            'v1-events-stream' => [
                'name' => 'Events real-time',
                'description' => 'Events stream',
                'path' => '/v1/events/stream',
                'verb' => 'get',
            ],
            'v1-config' => [
                'name' => 'Config',
                'description' => 'Configure your index',
                'path' => '/v1/index',
                'verb' => 'PUT',
            ],

            /*
             * Interaction endpoints
             */
            'v1-interaction' => [
                'name' => 'Interactions',
                'description' => 'Push interactions',
                'path' => '/v1/interaction',
                'verb' => 'get',
            ],
        ];
    }

    /**
     * Clean input with only valid elements.
     *
     * @param string[] $permissions
     *
     * @return string[]
     */
    public static function filter(array $permissions)
    {
        return array_intersect(
            $permissions,
            array_keys(self::all())
        );
    }

    /**
     * Read and Write endpoints.
     */
    public static function readWrite(): array
    {
        return array_keys(self::all());
    }

    /**
     * Index Write endpoints.
     */
    public static function indexWrite(): array
    {
        return [
            'v1-index-create',
            'v1-index-delete',
            'v1-items-index',
            'v1-items-delete',
            'v1-index-reset',
        ];
    }

    /**
     * Query endpoints.
     */
    public static function queryOnly(): array
    {
        return [
            'v1-query',
        ];
    }

    /**
     * Read endpoints.
     */
    public static function eventsOnly(): array
    {
        return [
            'v1-events',
            'v1-events-stream',
        ];
    }

    /**
     * Interaction endpoints.
     */
    public static function interactionOnly(): array
    {
        return [
            'v1-interaction',
        ];
    }

    /**
     * To composed.
     *
     * @param string[] $endpoints
     *
     * @return string[]
     */
    public static function compose(array $endpoints)
    {
        $all = self::all();

        return array_map(function (string $endpoint) use ($all) {
            return isset($all[$endpoint])
                ? $all[$endpoint]['verb'].'~~'.$all[$endpoint]['path']
                : '';
        }, $endpoints);
    }
}
