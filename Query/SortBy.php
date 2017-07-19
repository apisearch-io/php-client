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

namespace Puntmig\Search\Query;

/**
 * Class SortBy.
 */
class SortBy
{
    /**
     * @var array
     *
     * Sort by score
     */
    const SCORE = ['_score' => 'asc'];

    /**
     * @var array
     *
     * Sort random
     */
    const RANDOM = ['random'];

    /**
     * @var array
     *
     * Sort al-tun-tun
     */
    const AL_TUN_TUN = self::RANDOM;

    /**
     * @var array
     *
     * Sort by id ASC
     */
    const ID_ASC = ['uuid.id' => 'asc'];

    /**
     * @var array
     *
     * Sort by id DESC
     */
    const ID_DESC = ['uuid.id' => 'desc'];

    /**
     * @var array
     *
     * Sort by type ASC
     */
    const TYPE_ASC = ['uuid.type' => 'asc'];

    /**
     * @var array
     *
     * Sort by type DESC
     */
    const TYPE_DESC = ['uuid.type' => 'desc'];

    /**
     * @var array
     *
     * Sort by location ASC using KM
     */
    const LOCATION_KM_ASC = ['_geo_distance' => ['order' => 'asc', 'unit' => 'km']];

    /**
     * @var array
     *
     * Sort by location ASC using Miles
     */
    const LOCATION_MI_ASC = ['_geo_distance' => ['order' => 'asc', 'unit' => 'mi']];
}
