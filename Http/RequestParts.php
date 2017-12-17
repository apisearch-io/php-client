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
 * Class RequestParts.
 */
class RequestParts
{
    /**
     * @var string
     *
     * Url
     */
    private $url;

    /**
     * @var array
     *
     * Parameters
     */
    private $parameters;

    /**
     * @var array
     *
     * Options
     */
    private $options;

    /**
     * RequestComponents constructor.
     *
     * @param string $url
     * @param array  $parameters
     * @param array  $options
     */
    public function __construct(
        string $url,
        array $parameters,
        array $options
    ) {
        $this->url = $url;
        $this->parameters = $parameters;
        $this->options = $options;
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
     * Get parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
