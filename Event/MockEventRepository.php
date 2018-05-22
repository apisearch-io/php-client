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

use Apisearch\Exception\MockException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Query\Query;
use Apisearch\Result\Events;

/**
 * Class MockEventRepository.
 */
class MockEventRepository extends HttpRepositoryWithCredentials implements EventRepository
{
    /**
     * Create index.
     *
     * @throws ResourceExistsException
     */
    public function createIndex()
    {
        $this->throwMockException();
    }

    /**
     * Delete index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        $this->throwMockException();
    }

    /**
     * Save event.
     *
     * @param Event $event
     *
     * @throws ResourceNotAvailableException
     */
    public function save(Event $event)
    {
        $this->throwMockException();
    }

    /**
     * Query over events.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Events
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Events {
        $this->throwMockException();
    }

    /**
     * Throw exception.
     *
     * @throws MockException
     */
    private function throwMockException()
    {
        throw MockException::isAMock();
    }
}
