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

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * Class Config.
 */
class Config implements HttpTransportable
{
    /**
     * @var Campaigns
     *
     * Campaigns
     */
    private $campaigns;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->campaigns = new Campaigns();
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
        $config = new self();
        $config->campaigns = Campaigns::createFromArray($array['campaigns'] ?? []);

        return $config;
    }
}
