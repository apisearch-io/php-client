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
 * Class RepositoryBucket.
 */
class RepositoryBucket
{
    /**
     * @var TransformableRepository[]
     *
     * Repositories
     */
    private $repositories = [];

    /**
     * Add repository.
     *
     * @param string                  $appName
     * @param string                  $indexName
     * @param TransformableRepository $repository
     */
    public function addRepository(
        string $appName,
        string $indexName,
        TransformableRepository $repository
    ) {
        $this->repositories[$appName][$indexName] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $appName
     * @param string $indexName
     *
     * @return TransformableRepository|null
     */
    public function findRepository(
        string $appName,
        string $indexName
    ): ? TransformableRepository {
        if (
            !isset($this->repositories[$appName]) ||
            !isset($this->repositories[$appName][$indexName])
        ) {
            return null;
        }

        return $this->repositories[$appName][$indexName];
    }
}
