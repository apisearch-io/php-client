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

namespace Apisearch\Model;

use Apisearch\Exception\CoordinateException;
use Apisearch\Exception\UUIDException;

/**
 * Class Item.
 */
class Item implements HttpTransportable, UUIDReference
{
    /**
     * @var ItemUUID
     *
     * Item UUID
     */
    private $uuid;

    /**
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var float
     *
     * Distance
     */
    private $distance;

    /**
     * @var array
     *
     * Non indexed metadata
     */
    private $metadata;

    /**
     * @var array
     *
     * Indexed Metadata
     */
    private $indexedMetadata;

    /**
     * @var array
     *
     * Searchable metadata
     */
    private $searchableMetadata;

    /**
     * @var array
     *
     * Exact matching metadata
     */
    private $exactMatchingMetadata;

    /**
     * @var array
     *
     * Suggest
     */
    private $suggest;

    /**
     * @var array
     *
     * Non indexed highlights
     */
    private $highlights = [];

    /**
     * Item constructor.
     *
     * @param ItemUUID   $uuid
     * @param Coordinate $coordinate
     * @param array      $metadata
     * @param array      $indexedMetadata
     * @param array      $searchableMetadata
     * @param array      $exactMatchingMetadata
     * @param array      $suggest
     */
    private function __construct(
        ItemUUID $uuid,
        ? Coordinate $coordinate,
        array $metadata,
        array $indexedMetadata,
        array $searchableMetadata,
        array $exactMatchingMetadata,
        array $suggest
    ) {
        $this->uuid = $uuid;
        $this->coordinate = $coordinate;
        $this->metadata = $metadata;
        $this->indexedMetadata = $indexedMetadata;
        $this->searchableMetadata = $searchableMetadata;
        $this->exactMatchingMetadata = $exactMatchingMetadata;
        $this->suggest = $suggest;
    }

    /**
     * Create.
     *
     * @param ItemUUID $uuid
     * @param array    $metadata
     * @param array    $indexedMetadata
     * @param array    $searchableMetadata
     * @param array    $exactMatchingMetadata
     * @param array    $suggest
     *
     * @return Item
     */
    public static function create(
        ItemUUID $uuid,
        array $metadata = [],
        array $indexedMetadata = [],
        array $searchableMetadata = [],
        array $exactMatchingMetadata = [],
        array $suggest = []
    ) {
        return new self(
            $uuid,
            null,
            $metadata,
            $indexedMetadata,
            $searchableMetadata,
            $exactMatchingMetadata,
            $suggest
        );
    }

    /**
     * Create located.
     *
     * @param ItemUUID   $uuid
     * @param Coordinate $coordinate
     * @param array      $metadata
     * @param array      $indexedMetadata
     * @param array      $searchableMetadata
     * @param array      $exactMatchingMetadata
     * @param array      $suggest
     *
     * @return Item
     */
    public static function createLocated(
        ItemUUID $uuid,
        Coordinate $coordinate,
        array $metadata = [],
        array $indexedMetadata = [],
        array $searchableMetadata = [],
        array $exactMatchingMetadata = [],
        array $suggest = []
    ) {
        return new self(
            $uuid,
            $coordinate,
            $metadata,
            $indexedMetadata,
            $searchableMetadata,
            $exactMatchingMetadata,
            $suggest
        );
    }

    /**
     * Get ItemUUID.
     *
     * @return ItemUUID
     */
    public function getUUID(): ItemUUID
    {
        return $this->uuid;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this
            ->uuid
            ->getId();
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this
            ->uuid
            ->getType();
    }

    /**
     * Get Coordinate.
     *
     * @return null|Coordinate
     */
    public function getCoordinate(): ? Coordinate
    {
        return $this->coordinate;
    }

    /**
     * Get Distance.
     *
     * @return null|float
     */
    public function getDistance(): ? float
    {
        return $this->distance;
    }

    /**
     * Get Metadata.
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Set Metadata.
     *
     * @param array $metadata
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Add metadata.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addMetadata(
        string $key,
        $value
    ) {
        $this->metadata[$key] = $value;
    }

    /**
     * Get IndexedMetadata.
     *
     * @return array
     */
    public function getIndexedMetadata(): array
    {
        return $this->indexedMetadata;
    }

    /**
     * Set IndexedMetadata.
     *
     * @param array $indexedMetadata
     */
    public function setIndexedMetadata(array $indexedMetadata)
    {
        $this->indexedMetadata = $indexedMetadata;
    }

    /**
     * Add indexedMetadata.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addIndexedMetadata(
        string $key,
        $value
    ) {
        $this->indexedMetadata[$key] = $value;
    }

    /**
     * Get SearchableMetadata.
     *
     * @return array
     */
    public function getSearchableMetadata(): array
    {
        return $this->searchableMetadata;
    }

    /**
     * Set SearchableMetadata.
     *
     * @param array $searchableMetadata
     */
    public function setSearchableMetadata(array $searchableMetadata)
    {
        $this->searchableMetadata = $searchableMetadata;
    }

    /**
     * Add searchableMetadata.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addSearchableMetadata(
        string $key,
        $value
    ) {
        $this->searchableMetadata[$key] = $value;
    }

    /**
     * Get ExactMatchingMetadata.
     *
     * @return array
     */
    public function getExactMatchingMetadata(): array
    {
        return $this->exactMatchingMetadata;
    }

    /**
     * Set ExactMatchingMetadata.
     *
     * @param array $exactMatchingMetadata
     */
    public function setExactMatchingMetadata(array $exactMatchingMetadata)
    {
        $this->exactMatchingMetadata = $exactMatchingMetadata;
    }

    /**
     * Add exactMatchingMetadata.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function addExactMatchingMetadata(
        string $key,
        $value
    ) {
        $this->exactMatchingMetadata[$key] = $value;
    }

    /**
     * Get all metadata.
     *
     * @return array
     */
    public function getAllMetadata(): array
    {
        return array_merge(
            $this->metadata,
            $this->indexedMetadata
        );
    }

    /**
     * Get any element from any metadata.
     *
     * @param string $key
     *
     * @return null|mixed
     */
    public function get(string $key)
    {
        return $this->getAllMetadata()[$key] ?? null;
    }

    /**
     * Get suggest.
     *
     * @return array
     */
    public function getSuggest(): array
    {
        return $this->suggest;
    }

    /**
     * Get Highlights.
     *
     * @return array
     */
    public function getHighlights(): array
    {
        return $this->highlights;
    }

    /**
     * Get Highlights.
     *
     * @param string $fieldName
     *
     * @return string|null
     */
    public function getHighlight(string $fieldName): ? string
    {
        return isset($this->highlights[$fieldName])
            ? ((string) $this->highlights[$fieldName])
            : null;
    }

    /**
     * Set Highlights.
     *
     * @param array $highlights
     */
    public function setHighlights(array $highlights)
    {
        $this->highlights = $highlights;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'uuid' => $this->uuid->toArray(),
            'coordinate' => $this->coordinate instanceof Coordinate
                ? $this->coordinate->toArray()
                : null,
            'distance' => $this->distance,
            'metadata' => $this->metadata,
            'indexed_metadata' => $this->indexedMetadata,
            'searchable_metadata' => $this->searchableMetadata,
            'exact_matching_metadata' => $this->exactMatchingMetadata,
            'suggest' => $this->suggest,
            'highlights' => $this->highlights,
        ], function ($element) {
            return
            !(
                is_null($element) ||
                (is_array($element) && empty($element))
            );
        });
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return Item
     */
    public static function createFromArray(array $array): Item
    {
        if (
            !isset($array['uuid']) ||
            !is_array($array['uuid'])
        ) {
            throw UUIDException::createUUIDBadFormatException();
        }

        if (
            isset($array['coordinate']) &&
            !is_array($array['coordinate'])
        ) {
            throw CoordinateException::createCoordinateBadFormatException();
        }

        $item = isset($array['coordinate'])
            ? self::createLocated(
                ItemUUID::createFromArray($array['uuid']),
                Coordinate::createFromArray($array['coordinate']),
                $array['metadata'] ?? [],
                $array['indexed_metadata'] ?? [],
                $array['searchable_metadata'] ?? [],
                $array['exact_matching_metadata'] ?? [],
                $array['suggest'] ?? []
            )
            : self::create(
                ItemUUID::createFromArray($array['uuid']),
                $array['metadata'] ?? [],
                $array['indexed_metadata'] ?? [],
                $array['searchable_metadata'] ?? [],
                $array['exact_matching_metadata'] ?? [],
                $array['suggest'] ?? []
            );

        if (isset($array['distance'])) {
            $item->distance = (float) $array['distance'];
        }

        if (isset($array['highlights'])) {
            $item->highlights = $array['highlights'];
        }

        return $item;
    }

    /**
     * Compose unique id.
     *
     * @return string
     */
    public function composeUUID(): string
    {
        return $this
            ->uuid
            ->composeUUID();
    }

    /**
     * Magic property is set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->getAllMetadata());
    }

    /**
     * Magic get method.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
}
