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

use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Query\Query;
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Result\Logs;
use Exception;

/**
 * Class InMemoryLogRepository.
 */
class InMemoryLogRepository extends RepositoryWithCredentials implements LogRepository
{
    /**
     * @var array
     *
     * Logs
     */
    private $logs = [];

    /**
     * Create index.
     *
     * @throws ResourceExistsException
     */
    public function createIndex()
    {
        if (array_key_exists($this->getIndexKey(), $this->logs)) {
            throw ResourceExistsException::logsIndexExists();
        }

        $this->logs[$this->getIndexKey()] = [];
    }

    /**
     * Delete index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        if (!array_key_exists($this->getIndexKey(), $this->logs)) {
            throw ResourceNotAvailableException::logsIndexNotAvailable('Index not found in InMemoryLogRepository');
        }

        unset($this->logs[$this->getIndexKey()]);
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
     * @throws Exception
     */
    public function query(
        Query $query,
        ? int $from = null,
        ? int $to = null
    ): Logs {
        if (!array_key_exists($this->getIndexKey(), $this->logs)) {
            throw ResourceNotAvailableException::logsIndexNotAvailable('Index not found in InMemoryLogRepository');
        }

        throw new \Exception('Endpoint not implemented');
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
        if (!array_key_exists($this->getIndexKey(), $this->logs)) {
            throw ResourceNotAvailableException::logsIndexNotAvailable('Index not found in InMemoryLogRepository');
        }

        $this->logs[$this->getIndexKey()][] = $log;
    }

    /**
     * Get index position by credentials.
     *
     * @return string
     */
    private function getIndexKey(): string
    {
        return $this
            ->getRepositoryReference()
            ->compose();
    }
}
