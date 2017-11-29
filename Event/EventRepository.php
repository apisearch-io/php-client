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

namespace Apisearch\Event;

/**
 * Class EventRepository.
 */
interface EventRepository
{
    /**
     * Set app id.
     *
     * @param string $appId
     */
    public function setAppId(string $appId);

    /**
     * Create repository.
     *
     * @param bool $removeIfExists
     */
    public function createRepository(bool $removeIfExists = false);

    /**
     * Save event.
     *
     * @param Event $event
     */
    public function save(Event $event);

    /**
     * Get all events.
     *
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
     *
     * @return Event[]
     */
    public function all(
        string $name = null,
        ? int $from = null,
        ? int $to = null,
        ? int $length = 10,
        ? int $offset = 0
    ): array;

    /**
     * Get last event.
     *
     * @return Event|null
     */
    public function last(): ? Event;

    /**
     * Get stats.
     *
     * @param int|null $from
     * @param int|null $to
     *
     * @return Stats
     */
    public function stats(
        ? int $from = null,
        ? int $to = null
    ): Stats;
}
