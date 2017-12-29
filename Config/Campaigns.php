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

use Apisearch\Model\HttpTransportable;

/**
 * Class Campaigns.
 */
class Campaigns implements HttpTransportable
{
    /**
     * @var Campaign[]
     *
     * Campaigns
     */
    private $campaigns = [];

    /**
     * Add campaign.
     *
     * @param Campaign $campaign
     */
    public function addCampaign(Campaign $campaign)
    {
        $this->campaigns[] = $campaign;
    }

    /**
     * Get campaigns.
     *
     * @return Campaign[]
     */
    public function getCampaigns(): array
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
        return array_map(function (Campaign $campaign) {
            return $campaign->toArray();
        }, $this->campaigns);
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array)
    {
        $campaigns = new self();
        $campaigns->campaigns = array_map(function (array $campaign) {
            return Campaign::createFromArray($campaign);
        }, $array);

        return $campaigns;
    }
}
