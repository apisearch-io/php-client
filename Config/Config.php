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

namespace Apisearch\Config;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * Class Config.
 */
class Config implements HttpTransportable
{
    /**
     * @var string
     *
     * null|Language
     */
    private $language;

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
    private $campaigns;

    /**
     * Config constructor.
     *
     * @param null|string $language
     */
    public function __construct(?string $language)
    {
        $this->language = $language;
        $this->campaigns = new Campaigns();
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
     * Add synonym.
     *
     * @param Synonym $synonym
     */
    public function addSynonym(Synonym $synonym)
    {
        $this->synonyms = $synonym;
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
     */
    public function addCampaign(Campaign $campaign)
    {
        $this
            ->campaigns
            ->addCampaign($campaign);
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
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'language' => $this->language,
            'synonyms' => array_map(function (Synonym $synonym) {
                return $synonym->toArray();
            }, $this->synonyms),
            'campaigns' => $this
                ->campaigns
                ->toArray(),
        ]);
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
        $config = new self(
            ($array['language'] ?? null)
        );

        $config->synonyms = array_map(function (array $synonym) {
            return Synonym::createFromArray($synonym);
        }, $array['synonyms'] ?? []);

        $config->campaigns = Campaigns::createFromArray($array['campaigns'] ?? []);

        return $config;
    }
}
