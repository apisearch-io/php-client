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

namespace Apisearch\Http;

/**
 * Class RetryMap.
 */
class RetryMap
{
    /**
     * @var Retry[]
     *
     * Retry elements
     */
    private $retries = [];

    /**
     * Add retry.
     *
     * @param Retry $retry
     */
    public function addRetry(Retry $retry)
    {
        $this->retries[$retry->getUrl().'~~'.$retry->getMethod()] = $retry;
    }

    /**
     * Create from array.
     *
     * @param array $data
     *
     * @return RetryMap
     */
    public static function createFromArray(array $data): RetryMap
    {
        $retryMap = new RetryMap();
        foreach ($data as $entry) {
            $retryMap->addRetry(Retry::createFromArray($entry));
        }

        return $retryMap;
    }

    /**
     * Check retry.
     *
     * @param string $url
     * @param string $method
     *
     * @return Retry|null
     */
    public function getRetry(
        string $url,
        string $method
    ): ? Retry {
        $url = trim(trim(strtolower($url)), '/');
        $method = trim(strtolower($method));

        if (isset($this->retries[$url.'~~'.$method])) {
            return $this->retries[$url.'~~'.$method];
        }

        if (isset($this->retries['*~~'.$method])) {
            return $this->retries['*~~'.$method];
        }

        if (isset($this->retries[$url.'~~*'])) {
            return $this->retries[$url.'~~*'];
        }

        if (isset($this->retries['*~~*'])) {
            return $this->retries['*~~*'];
        }

        return null;
    }
}
