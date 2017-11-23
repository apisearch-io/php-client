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

use Puntmig\Search\Model\HttpTransportable;

/**
 * Class Stats.
 */
class Stats implements HttpTransportable
{
    /**
     * @var int[]
     *
     * Event counter
     */
    private $eventCounter;

    /**
     * Stats constructor.
     *
     * @param array $eventCounter
     */
    private function __construct(array $eventCounter)
    {
        $this->eventCounter = $eventCounter;
    }

    /**
     * Get EventCounter.
     *
     * @return int[]
     */
    public function getEventCounter(): array
    {
        return $this->eventCounter;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'ev' => $this->eventCounter,
        ], function ($element) {
            return
            !(
                is_null($element) ||
                (is_array($element) && empty($element))
            );
        });
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array)
    {
        return self::createByPlainData(
            $array['ev'] ?? []
        );
    }

    /**
     * Stats constructor.
     *
     * @param array $eventCounter
     *
     * @return Stats
     */
    public static function createByPlainData(array $eventCounter): Stats
    {
        return new self(
            $eventCounter
        );
    }
}
