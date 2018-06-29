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
 * Class ImmutableConfig.
 */
class ImmutableConfig implements HttpTransportable
{
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
    private $storeSearchableMetadata = true;

    /**
     * @var Synonym[]
     *
     * Synonyms
     */
    private $synonyms = [];

    /**
     * ImmutableConfig constructor.
     *
     * @param null|string $language
     * @param bool        $storeSearchableMetadata
     */
    public function __construct(
        ?string $language = null,
        bool $storeSearchableMetadata
    ) {
        $this->language = $language;
        $this->storeSearchableMetadata = $storeSearchableMetadata;
    }

    /**
     * Get language.
     *
     * @return null|string
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
     */
    public function addSynonym(Synonym $synonym)
    {
        $this->synonyms[] = $synonym;
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

        $config->synonyms = array_map(function (array $synonym) {
            return Synonym::createFromArray($synonym);
        }, $array['synonyms'] ?? []);

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
