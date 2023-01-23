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
use Apisearch\Repository\RepositoryReference;

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
     * @var bool
     *
     * Promoted
     */
    private $promoted = false;

    /**
     * @var float
     *
     * Score
     */
    private $score;

    /**
     * @var AppUUID|null
     */
    private $appUUID;

    /**
     * @var IndexUUID|null
     */
    private $indexUUID;

    /**
     * Item constructor.
     *
     * @param ItemUUID        $uuid
     * @param Coordinate|null $coordinate
     * @param array           $metadata
     * @param array           $indexedMetadata
     * @param array           $searchableMetadata
     * @param array           $exactMatchingMetadata
     * @param array           $suggest
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
        return new static(
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
        return new static(
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
     * @return Coordinate|null
     */
    public function getCoordinate(): ? Coordinate
    {
        return $this->coordinate;
    }

    /**
     * Get Distance.
     *
     * @return float|null
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
     * @param string $key
     */
    public function deleteMetadata(string $key)
    {
        unset($this->metadata[$key]);
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
     * @param string $key
     */
    public function deleteIndexedMetadata(string $key)
    {
        unset($this->indexedMetadata[$key]);
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
     * @param string $key
     */
    public function deleteSearchableMetadata(string $key)
    {
        unset($this->searchableMetadata[$key]);
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
     * @param string $key
     */
    public function deleteExactMatchingMetadata(string $key)
    {
        unset($this->exactMatchingMetadata[$key]);
    }

    /**
     * Get all metadata.
     *
     * @return array
     */
    public function getAllMetadata(): array
    {
        return array_merge(
            $this->indexedMetadata,
            $this->metadata
        );
    }

    /**
     * Get any element from any metadata.
     *
     * @param string $key
     *
     * @return mixed|null
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
     * Set is promoted.
     */
    public function setPromoted()
    {
        $this->promoted = true;
    }

    /**
     * Is promoted.
     *
     * @return bool
     */
    public function isPromoted(): bool
    {
        return $this->promoted;
    }

    /**
     * Get Score.
     *
     * @return float|null
     */
    public function getScore(): ? float
    {
        return $this->score;
    }

    /**
     * Set Score.
     *
     * @param float $score
     *
     * @return Item
     */
    public function setScore(float $score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @param RepositoryReference $repositoryReference
     */
    public function setRepositoryReference(RepositoryReference $repositoryReference)
    {
        $this->appUUID = $repositoryReference->getAppUUID();
        $this->indexUUID = $repositoryReference->getIndexUUID();
    }

    /**
     * @return AppUUID|null
     */
    public function getAppUUID(): ?AppUUID
    {
        return $this->appUUID;
    }

    /**
     * @return IndexUUID|null
     */
    public function getIndexUUID(): ?IndexUUID
    {
        return $this->indexUUID;
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
            'is_promoted' => !$this->promoted ? null : true,
            'score' => $this->score,
            'app_uuid' => $this->appUUID instanceof AppUUID
                ? $this->appUUID->toArray()
                : null,
            'index_uuid' => $this->indexUUID instanceof IndexUUID
                ? $this->indexUUID->toArray()
                : null,
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
    public static function createFromArray(array $array): self
    {
        if (
            !isset($array['uuid']) ||
            !is_array($array['uuid'])
        ) {
            throw InvalidFormatException::itemUUIDRepresentationNotValid($array['uuid'] ?? []);
        }

        if (
            isset($array['coordinate']) &&
            !is_array($array['coordinate'])
        ) {
            throw InvalidFormatException::coordinateFormatNotValid();
        }

        $item = isset($array['coordinate'])
            ? static::createLocated(
                ItemUUID::createFromArray($array['uuid']),
                Coordinate::createFromArray($array['coordinate']),
                $array['metadata'] ?? [],
                $array['indexed_metadata'] ?? [],
                $array['searchable_metadata'] ?? [],
                $array['exact_matching_metadata'] ?? [],
                $array['suggest'] ?? []
            )
            : static::create(
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

        if (isset($array['is_promoted']) && true === $array['is_promoted']) {
            $item->setPromoted();
        }

        if (isset($array['score']) && !is_null($array['score'])) {
            $item->setScore((float) $array['score']);
        }

        if (isset($array['app_uuid']) && !is_null($array['app_uuid'])) {
            $item->appUUID = AppUUID::createFromArray($array['app_uuid']);
        }

        if (isset($array['index_uuid']) && !is_null($array['index_uuid'])) {
            $item->indexUUID = IndexUUID::createFromArray($array['index_uuid']);
        }

        return $item;
    }

    /**
     * Get path by field.
     *
     * @param string $field
     *
     * @return string
     */
    public static function getPathByField(string $field)
    {
        if (0 === strpos($field, 'indexed_metadata.')) {
            return $field;
        }

        if ('uuid' === $field) {
            return '_id';
        }

        if ('_id' === $field) {
            return $field;
        }

        return in_array($field, ['id', 'type'])
            ? 'uuid.'.$field
            : 'indexed_metadata.'.$field;
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

    /**
     * @param callable $callable
     *
     * @return void
     */
    public function map(callable $callable)
    {
        $array = $callable($this->toArray());
        $this->metadata = $array['metadata'] ?? [];
        $this->indexedMetadata = $array['indexed_metadata'] ?? [];
        $this->searchableMetadata = $array['searchable_metadata'] ?? [];
        $this->exactMatchingMetadata = $array['exact_matching_metadata'] ?? [];
        $this->suggest = $array['suggest'] ?? [];
        $this->highlights = $array['highlights'] ?? [];
        $this->promoted = isset($array['is_promoted']) && true === $array['is_promoted'];
        $this->score = $array['score'] ?? null;
        $this->coordinate = is_array($array['coordinate'] ?? null)
            ? Coordinate::createFromArray($array['coordinate'])
            : null;
    }
}
