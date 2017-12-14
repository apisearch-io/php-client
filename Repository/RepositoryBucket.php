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
     * @param string                  $name
     * @param string                  $index
     * @param TransformableRepository $repository
     */
    public function addRepository(
        string $name,
        string $index,
        TransformableRepository $repository
    ) {
        $this->repositories[$name][$index] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $name
     * @param string $index
     *
     * @return TransformableRepository|null
     */
    public function findRepository(
        string $name,
        string $index
    ): ? TransformableRepository {
        if (
            !isset($this->repositories[$name]) ||
            !isset($this->repositories[$name][$index])
        ) {
            return null;
        }

        return $this->repositories[$name][$index];
    }
}
