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

namespace Apisearch\Model;

use Apisearch\Exception\InvalidFormatException;
use Carbon\Carbon;

/**
 * File header placeholder.
 */
class Token implements HttpTransportable
{
    /**
     * @var int
     *
     * No cache
     */
    const NO_CACHE = 0;

    /**
     * @var int
     *
     * Default ttl
     */
    const DEFAULT_TTL = 60;

    /**
     * @var TokenUUID
     *
     * Token uuid
     */
    private $tokenUUID;

    /**
     * @var AppUUID
     *
     * App uuid
     */
    private $appUUID;

    /**
     * @var int
     *
     * Created at
     */
    private $createdAt;

    /**
     * @var int
     *
     * Updated at
     */
    private $updatedAt;

    /**
     * @var IndexUUID[]
     *
     * Indices
     */
    private $indices;

    /**
     * @var array
     *
     * Endpoints enabled
     */
    private $endpoints;

    /**
     * @var string[]
     *
     * Plugins
     */
    private $plugins;

    /**
     * @var int
     *
     * TTL
     */
    private $ttl;

    /**
     * @var array
     *
     * Metadata
     */
    private $metadata = [];

    /**
     * PushToken constructor.
     *
     * @param TokenUUID   $tokenUUID
     * @param AppUUID     $appUUID
     * @param IndexUUID[] $indices
     * @param string[]    $endpoints
     * @param string[]    $plugins
     * @param int         $ttl
     * @param array       $metadata
     */
    public function __construct(
        TokenUUID $tokenUUID,
        AppUUID $appUUID,
        array $indices = [],
        array $endpoints = [],
        array $plugins = [],
        int $ttl = self::DEFAULT_TTL,
        array $metadata = []
    ) {
        $this->tokenUUID = $tokenUUID;
        $this->appUUID = $appUUID;
        $this->createdAt = Carbon::now('UTC')->timestamp;
        $this->updatedAt = $this->createdAt;
        $this->setIndices($indices);
        $this->setEndpoints($endpoints);
        $this->setPlugins($plugins);
        $this->ttl = $ttl;
        $this->metadata = $metadata;
    }

    /**
     * Get TokenUUID.
     *
     * @return TokenUUID
     */
    public function getTokenUUID(): TokenUUID
    {
        return $this->tokenUUID;
    }

    /**
     * Get AppUUID.
     *
     * @return AppUUID
     */
    public function getAppUUID(): AppUUID
    {
        return $this->appUUID;
    }

    /**
     * Get CreatedAt.
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Get UpdatedAt.
     *
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * Get Indices.
     *
     * @return string[]
     */
    public function getIndices(): array
    {
        return $this->indices;
    }

    /**
     * Set indices.
     *
     * @param IndexUUID[] $indices
     */
    public function setIndices(array $indices)
    {
        $this->indices = $indices;
    }

    /**
     * Get Endpoints.
     *
     * @return array
     */
    public function getEndpoints(): array
    {
        return $this->endpoints;
    }

    /**
     * Set Endpoints.
     *
     * @param array $endpoints
     */
    public function setEndpoints(array $endpoints)
    {
        $this->endpoints = array_values(
            array_unique(
                array_map(function ($endpoint) {
                    list($method, $route) = explode('~~', $endpoint);

                    return $method.'~~'.trim($route, '/');
                }, array_filter($endpoints))
            )
        );
    }

    /**
     * Get Plugins.
     *
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * Has plugin.
     *
     * @param string $pluginName
     *
     * @return bool
     */
    public function hasPlugin(string $pluginName): bool
    {
        return in_array(
            $pluginName,
            $this->getPlugins()
        );
    }

    /**
     * Set Plugins.
     *
     * @param array $plugins
     */
    public function setPlugins(array $plugins)
    {
        $this->plugins = array_values(
            array_unique(
                array_filter(
                    $plugins
                )
            )
        );
    }

    /**
     * Get TTL.
     *
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * Set metadata value.
     *
     * @param string $field
     * @param mixed  $value
     */
    public function setMetadataValue(
        string $field,
        $value
    ) {
        $this->metadata[$field] = $value;
    }

    /**
     * Get metadata value.
     *
     * @param string $field
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    public function getMetadataValue(
        string $field,
        $defaultValue = null
    ) {
        return $this->metadata[$field] ?? $defaultValue;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->tokenUUID->toArray(),
            'app_uuid' => $this->appUUID->toArray(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'indices' => array_map(function (IndexUUID $indexUUID) {
                return $indexUUID->toArray();
            }, $this->indices),
            'endpoints' => $this->endpoints,
            'plugins' => $this->plugins,
            'ttl' => $this->ttl,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        if (
            !isset($array['uuid']) ||
            !isset($array['app_uuid'])
        ) {
            throw InvalidFormatException::tokenFormatNotValid(json_encode($array));
        }

        $token = new self(
            TokenUUID::createFromArray($array['uuid']),
            AppUUID::createFromArray($array['app_uuid']),
            array_map(function (array $indexUUIDAsArray) {
                return IndexUUID::createFromArray($indexUUIDAsArray);
            }, ($array['indices'] ?? [])),
            $array['endpoints'] ?? [],
            $array['plugins'] ?? [],
            $array['ttl'] ?? self::DEFAULT_TTL,
            $array['metadata'] ?? []
        );

        $token->createdAt = $array['created_at'];
        $token->updatedAt = $array['updated_at'];

        return $token;
    }
}
