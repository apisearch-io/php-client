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

namespace Apisearch\Log;

/**
 * Class LogRepositoryBucket.
 */
class LogRepositoryBucket
{
    /**
     * @var LogRepository[]
     *
     * Repositories
     */
    private $repositories = [];

    /**
     * Add repository.
     *
     * @param string        $name
     * @param LogRepository $repository
     */
    public function addRepository(
        string $name,
        LogRepository $repository
    ) {
        $this->repositories[$name] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $name
     *
     * @return LogRepository|null
     */
    public function findRepository(string $name): ? LogRepository
    {
        return isset($this->repositories[$name])
            ? $this->repositories[$name]
            : null;
    }
}
