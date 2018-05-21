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
 * Class Retry.
 */
class Retry
{
    /**
     * @var int
     *
     * Default microseconds between retries
     */
    const DEFAULT_MICROSECONDS_BETWEEN_RETRIES = 1000;

    /**
     * @param string
     *
     * Url
     */
    private $url;

    /**
     * @param string
     *
     * Method
     */
    private $method;

    /**
     * @param int
     *
     * Retries
     */
    private $retries;

    /**
     * @param int
     *
     * Microseconds between retries
     */
    private $microsecondsBetweenRetries;

    /**
     * Retry constructor.
     *
     * @param string $url
     * @param string $method
     * @param int    $retries
     * @param int    $microsecondsBetweenRetries
     */
    public function __construct(
        string $url,
        string $method,
        int $retries,
        int $microsecondsBetweenRetries
    ) {
        $this->url = $url;
        $this->method = $method;
        $this->retries = $retries;
        $this->microsecondsBetweenRetries = $microsecondsBetweenRetries;
    }

    /**
     * Create from array.
     *
     * @param array $data
     *
     * @return Retry
     */
    public static function createFromArray(array $data): Retry
    {
        return new Retry(
            (string) (trim(trim(strtolower($data['url'] ?? '*')), '/')),
            (string) (trim(strtolower($data['method'] ?? '*'))),
            (int) ($data['retries'] ?? 0),
            (int) ($data['microseconds_between_retries'] ?? self::DEFAULT_MICROSECONDS_BETWEEN_RETRIES)
        );
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get retries.
     *
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * Get seconds between retries.
     *
     * @return int
     */
    public function getMicrosecondsBetweenRetries(): int
    {
        return $this->microsecondsBetweenRetries;
    }
}
