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

namespace Apisearch\Token;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;
use Carbon\Carbon;

/**
 * File header placeholder.
 */
class Token implements HttpTransportable
{
    /**
     * @var TokenUUID
     *
     * Token uuid
     */
    private $tokenUUID;

    /**
     * @var string
     *
     * App id
     */
    private $appId;

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
     * @var string[]
     *
     * Indices
     */
    private $indices;

    /**
     * @var int
     *
     * Seconds valid
     */
    private $secondsValid;

    /**
     * @var int
     *
     * Max hits per query
     */
    private $maxHitsPerQuery;

    /**
     * @var string[]
     *
     * HTTP Referrers
     */
    private $httpReferrers;

    /**
     * @var array
     *
     * Endpoints enabled
     */
    private $endpoints;

    /**
     * PushToken constructor.
     *
     * @param TokenUUID $tokenUUID
     * @param string    $appId
     * @param string[]  $indices
     * @param int       $secondsValid
     * @param int       $maxHitsPerQuery
     * @param string[]  $httpReferrers
     * @param string[]  $endpoints
     */
    public function __construct(
        TokenUUID $tokenUUID,
        string $appId,
        array $indices = [],
        int $secondsValid = 0,
        int $maxHitsPerQuery = 0,
        array $httpReferrers = [],
        array $endpoints = []
    ) {
        $this->tokenUUID = $tokenUUID;
        $this->appId = $appId;
        $this->createdAt = Carbon::now('UTC')->timestamp;
        $this->updatedAt = $this->createdAt;
        $this->setIndices($indices);
        $this->secondsValid = $secondsValid;
        $this->maxHitsPerQuery = $maxHitsPerQuery;
        $this->setHttpReferrers($httpReferrers);
        $this->setEndpoints($endpoints);
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
     * Get AppId
     *
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
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
     * @param string[] $indices
     */
    public function setIndices(array $indices)
    {
        $this->indices = array_values(
            array_unique(
                array_filter(
                    $indices
                )
            )
        );
    }

    /**
     * Get SecondsValid.
     *
     * @return int
     */
    public function getSecondsValid(): int
    {
        return $this->secondsValid;
    }

    /**
     * Set SecondsValid.
     *
     * @param int $secondsValid
     */
    public function setSecondsValid(int $secondsValid)
    {
        $this->secondsValid = $secondsValid;
    }

    /**
     * Get MaxHitsPerQuery.
     *
     * @return int
     */
    public function getMaxHitsPerQuery(): int
    {
        return $this->maxHitsPerQuery;
    }

    /**
     * Set MaxHitsPerQuery.
     *
     * @param int $maxHitsPerQuery
     */
    public function setMaxHitsPerQuery(int $maxHitsPerQuery)
    {
        $this->maxHitsPerQuery = $maxHitsPerQuery;
    }

    /**
     * Get HttpReferrers.
     *
     * @return string[]
     */
    public function getHttpReferrers(): array
    {
        return $this->httpReferrers;
    }

    /**
     * Set HttpReferrers.
     *
     * @param string[] $httpReferrers
     */
    public function setHttpReferrers(array $httpReferrers)
    {
        $this->httpReferrers = array_values(
            array_unique(
                array_filter(
                    $httpReferrers
                )
            )
        );
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
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->tokenUUID->toArray(),
            'app_id' => $this->appId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'indices' => $this->indices,
            'seconds_valid' => $this->secondsValid,
            'max_hits_per_query' => $this->maxHitsPerQuery,
            'http_referrers' => $this->httpReferrers,
            'endpoints' => $this->endpoints,
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
            !isset($array['app_id'])
        ) {
            throw InvalidFormatException::tokenFormatNotValid(json_encode($array));
        }

        $token = new self(
            TokenUUID::createFromArray($array['uuid']),
            $array['app_id'],
            $array['indices'] ?? [],
            $array['seconds_valid'] ?? 0,
            $array['max_hits_per_query'] ?? 0,
            $array['http_referrers'] ?? [],
            $array['endpoints'] ?? []
        );

        $token->createdAt = $array['created_at'];
        $token->updatedAt = $array['updated_at'];

        return $token;
    }
}
