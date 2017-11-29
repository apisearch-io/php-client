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

namespace Apisearch\Repository;

/**
 * Class RepositoryWithCredentials.
 */
abstract class RepositoryWithCredentials
{
    /**
     * @var string
     *
     * App id
     */
    private $appId;

    /**
     * @var string
     *
     * Api key
     */
    private $key;

    /**
     * Set credentials.
     *
     * @param string $appId
     * @param string $key
     */
    public function setCredentials(
        string $appId,
        string $key
    ) {
        $this->setAppId($appId);
        $this->key = $key;
    }

    /**
     * Set app id.
     *
     * @param string $appId
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * Get AppId.
     *
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey(): ? string
    {
        return $this->key;
    }
}
