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

use Apisearch\Config\Config;
use Apisearch\Exception\InvalidFormatException;

/**
 * Class Index.
 */
class Index implements HttpTransportable
{
    /**
     * @var IndexUUID
     *
     * Index UUID
     */
    private $uuid;

    /**
     * App id.
     *
     * @var AppUUID
     */
    private $appUUID;

    /**
     * Is OK.
     *
     * @var bool
     */
    private $isOK;

    /**
     * Doc count.
     *
     * @var int
     */
    private $docCount;

    /**
     * Size.
     *
     * @var string
     */
    private $size;

    /**
     * Shards.
     *
     * @var int
     */
    private $shards;

    /**
     * Replicas.
     *
     * @var int
     */
    private $replicas;

    /**
     * Fields.
     *
     * @var string[]
     */
    private $fields = [];

    /**
     * Metadata.
     *
     * @var array
     */
    private $metadata;

    /**
     * Index constructor.
     *
     * @param IndexUUID $uuid
     * @param AppUUID   $appUUID
     * @param bool      $isOK
     * @param int       $docCount
     * @param string    $size
     * @param int       $shards
     * @param int       $replicas
     * @param string[]  $fields
     * @param array     $metadata
     */
    public function __construct(
        IndexUUID $uuid,
        AppUUID $appUUID,
        bool $isOK,
        int $docCount = 0,
        string $size,
        int $shards,
        int $replicas,
        array $fields = [],
        array $metadata = []
    ) {
        $this->uuid = $uuid;
        $this->appUUID = $appUUID;
        $this->isOK = $isOK;
        $this->docCount = $docCount;
        $this->size = $size;
        $this->shards = $shards;
        $this->replicas = $replicas;
        $this->fields = $fields;
        $this->metadata = $metadata;
    }

    /**
     * Get IndexUUID.
     *
     * @return IndexUUID
     */
    public function getUUID(): IndexUUID
    {
        return $this->uuid;
    }

    /**
     * Get app id.
     *
     * @return AppUUID
     */
    public function getAppUUID(): AppUUID
    {
        return $this->appUUID;
    }

    /**
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->isOK;
    }

    /**
     * Get doc count.
     *
     * @return int
     */
    public function getDocCount(): int
    {
        return $this->docCount;
    }

    /**
     * Get size.
     *
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * Get Shards.
     *
     * @return int
     */
    public function getShards(): int
    {
        return $this->shards;
    }

    /**
     * Get Replicas.
     *
     * @return int
     */
    public function getReplicas(): int
    {
        return $this->replicas;
    }

    /**
     * Get fields.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
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
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid->toArray(),
            'app_id' => $this->appUUID->toArray(),
            'is_ok' => $this->isOK,
            'doc_count' => $this->docCount,
            'size' => $this->size,
            'shards' => $this->shards,
            'replicas' => $this->replicas,
            'fields' => $this->fields,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return Index
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array): self
    {
        if (!isset($array['uuid'], $array['app_id'])) {
            throw InvalidFormatException::indexFormatNotValid();
        }

        return new self(
            IndexUUID::createFromArray($array['uuid']),
            AppUUID::createFromArray($array['app_id']),
            $array['is_ok'] ?? false,
            $array['doc_count'] ?? 0,
            $array['size'] ?? '0kb',
            $array['shards'] ?? Config::DEFAULT_SHARDS,
            $array['replicas'] ?? Config::DEFAULT_REPLICAS,
            $array['fields'] ?? [],
            $array['metadata'] ?? []
        );
    }
}
