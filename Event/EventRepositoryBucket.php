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

namespace Apisearch\Event;

/**
 * Class EventRepositoryBucket.
 */
class EventRepositoryBucket
{
    /**
     * @var EventRepository[]
     *
     * Repositories
     */
    private $repositories = [];

    /**
     * Add repository.
     *
     * @param string          $name
     * @param EventRepository $repository
     */
    public function addRepository(
        string $name,
        EventRepository $repository
    ) {
        $this->repositories[$name] = $repository;
    }

    /**
     * Get repository by name and index.
     *
     * @param string $name
     *
     * @return EventRepository|null
     */
    public function findRepository(string $name): ? EventRepository
    {
        return isset($this->repositories[$name])
            ? $this->repositories[$name]
            : null;
    }
}
