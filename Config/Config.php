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

namespace Apisearch\Config;

use Apisearch\Model\HttpTransportable;

/**
 * Class Config.
 */
class Config implements HttpTransportable
{
    /**
     * @var int
     *
     * Default shards
     */
    const DEFAULT_SHARDS = 1;

    /**
     * @var int
     *
     * Default replicas
     */
    const DEFAULT_REPLICAS = 0;

    /**
     * @var string|null
     *
     * Language
     */
    private $language;

    /**
     * @var bool
     *
     * Store searchable metadata
     */
    private $storeSearchableMetadata;

    /**
     * @var Synonym[]
     *
     * Synonyms
     */
    private $synonyms = [];

    /**
     * @var Campaigns
     *
     * Campaigns
     */
    private $campaigns = [];

    /**
     * @var int
     *
     * Shards
     */
    private $shards;

    /**
     * @var int
     *
     * Replicas
     */
    private $replicas;

    /**
     * Config constructor.
     *
     * @param string|null $language
     * @param bool        $storeSearchableMetadata
     * @param int         $shards
     * @param int         $replicas
     */
    public function __construct(
        ?string $language = null,
        bool $storeSearchableMetadata = true,
        int $shards = self::DEFAULT_SHARDS,
        int $replicas = self::DEFAULT_REPLICAS
    ) {
        $this->language = $language;
        $this->storeSearchableMetadata = $storeSearchableMetadata;
        $this->shards = $shards;
        $this->replicas = $replicas;
        $this->campaigns = new Campaigns();
    }

    /**
     * Get language.
     *
     * @return string|null
     */
    public function getLanguage(): ? string
    {
        return $this->language;
    }

    /**
     * Get if searchable metadata is stored.
     *
     * @return bool
     */
    public function shouldSearchableMetadataBeStored(): ? bool
    {
        return $this->storeSearchableMetadata;
    }

    /**
     * Add synonym.
     *
     * @param Synonym $synonym
     *
     * @return Config
     */
    public function addSynonym(Synonym $synonym): Config
    {
        $this->synonyms[] = $synonym;

        return $this;
    }

    /**
     * get synonyms.
     *
     * @return Synonym[]
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * Add campaign.
     *
     * @param Campaign $campaign
     *
     * @return Config
     */
    public function addCampaign(Campaign $campaign): Config
    {
        $this
            ->campaigns
            ->addCampaign($campaign);

        return $this;
    }

    /**
     * Get campaigns.
     *
     * @return Campaigns
     */
    public function getCampaigns(): Campaigns
    {
        return $this->campaigns;
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
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'language' => $this->language,
            'store_searchable_metadata' => ($this->storeSearchableMetadata ? null : false),
            'synonyms' => array_map(function (Synonym $synonym) {
                return $synonym->toArray();
            }, $this->synonyms),
            'campaigns' => $this
                ->campaigns
                ->toArray(),
            'shards' => $this->shards,
            'replicas' => $this->replicas,
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
     * @return self
     */
    public static function createFromArray(array $array): self
    {
        $config = new self(
            ($array['language'] ?? null),
            ($array['store_searchable_metadata'] ?? true)
        );

        $config->campaigns = Campaigns::createFromArray($array['campaigns'] ?? []);
        $config->synonyms = array_map(function (array $synonym) {
            return Synonym::createFromArray($synonym);
        }, $array['synonyms'] ?? []);
        $config->shards = (int) ($array['shards'] ?? self::DEFAULT_SHARDS);
        $config->replicas = (int) ($array['replicas'] ?? self::DEFAULT_REPLICAS);

        return $config;
    }

    /**
     * Create empty.
     *
     * @return self
     */
    public static function createEmpty(): self
    {
        return self::createFromArray([]);
    }
}
