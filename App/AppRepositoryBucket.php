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

namespace Apisearch\App;

/**
 * Class AppRepositoryBucket.
 */
class AppRepositoryBucket
{
    /**
     * @var AppRepository[]
     *
     * Repositories
     */
    private $repositories = [];

    /**
     * @var array
     *
     * Repositories configuration
     */
    private $configuration;

    /**
     * AppRepositoryBucket constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Add repository.
     *
     * @param string        $appName
     * @param AppRepository $repository
     */
    public function addRepository(
        string $appName,
        AppRepository $repository
    ) {
        $this->repositories[$appName] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $appName
     *
     * @return AppRepository|null
     */
    public function findRepository(string $appName): ? AppRepository
    {
        return isset($this->repositories[$appName])
            ? $this->repositories[$appName]
            : null;
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
