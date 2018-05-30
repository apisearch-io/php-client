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

namespace Apisearch\User;

/**
 * Class UserRepositoryBucket.
 */
class UserRepositoryBucket
{
    /**
     * @var UserRepository[]
     *
     * Repositories
     */
    private $repositories = [];

    /**
     * Add repository.
     *
     * @param string         $name
     * @param UserRepository $repository
     */
    public function addRepository(
        string $name,
        UserRepository $repository
    ) {
        $this->repositories[$name] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $name
     *
     * @return UserRepository|null
     */
    public function findRepository(string $name): ? UserRepository
    {
        return isset($this->repositories[$name])
            ? $this->repositories[$name]
            : null;
    }
}
