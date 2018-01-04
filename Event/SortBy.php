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
 * Class SortBy.
 */
class SortBy
{
    /**
     * @var array
     *
     * Sort by id ASC
     */
    const OCCURRED_ON_ASC = 'asc';

    /**
     * @var array
     *
     * Sort by id DESC
     */
    const OCCURRED_ON_DESC = 'desc';
}
