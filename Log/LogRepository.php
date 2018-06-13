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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Query\Query;
use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Result\Logs;

/**
 * Class LogRepository.
 */
interface LogRepository extends WithRepositoryReference
{
    /**
     * Save log.
     *
     * @param Log $log
     *
     * @throws ResourceNotAvailableException
     */
    public function save(Log $log);

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
    ): Logs;
}
