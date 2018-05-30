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
}
