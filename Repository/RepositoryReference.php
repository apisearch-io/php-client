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
 * Class RepositoryReference.
 */
class RepositoryReference
{
    /**
     * @var string
     *
     * App id
     */
    protected $appId;

    /**
     * @var string
     *
     * Index
     */
    protected $index;

    /**
     * RepositoryReference constructor.
     *
     * @param string $appId
     * @param string $index
     */
    private function __construct(
        string $appId,
        string $index
    ) {
        $this->appId = $appId;
        $this->index = $index;
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
     * Get Index.
     *
     * @return string
     */
    public function getIndex(): string
    {
        return $this->index;
    }

    /**
     * Create by app id and token.
     *
     * @param string $appId
     * @param string $index
     *
     * @return RepositoryReference
     */
    public static function create(
        string $appId,
        string $index
    ) {
        return new self($appId, $index);
    }

    /**
     * Compose.
     *
     * @return string
     */
    public function compose(): string
    {
        return "{$this->appId}_{$this->index}";
    }
}
