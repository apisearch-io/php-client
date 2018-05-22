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

namespace Apisearch\Log;

use Apisearch\Exception\MockException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Query\Query;
use Apisearch\Result\Logs;

/**
 * Class MockLogRepository.
 */
class MockLogRepository extends HttpRepositoryWithCredentials implements LogRepository
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
     * Save log.
     *
     * @param Log $log
     *
     * @throws ResourceNotAvailableException
     */
    public function save(Log $log)
    {
        $this->throwMockException();
    }

    /**
     * Query over logs.
     *
     * @param Query    $query
     * @param int|null $from
     * @param int|null $to
     *
     * @return Logs
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Logs {
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
