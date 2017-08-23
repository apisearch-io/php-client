<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Event;

/**
 * Class EventRepository.
 */
interface EventRepository
{
    /**
     * Save event.
     *
     * @param Event $event
     */
    public function save(Event $event);

    /**
     * Get all events.
     *
     * @param string|null $key
     * @param string|null $name
     * @param int|null    $from
     * @param int|null    $to
     * @param int|null    $length
     * @param int|null    $offset
     *
     * @return Event[]
     */
    public function all(
        string $key = null,
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
}
