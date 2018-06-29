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

namespace Apisearch\Config;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;
use Carbon\Carbon;
use DateTime;

/**
 * Class Campaign.
 */
class Campaign implements HttpTransportable
{
    /**
     * @var int
     *
     * Mode Boost
     */
    const MODE_BOOST = 0;

    /**
     * @var int
     *
     * Mode List
     */
    const MODE_LIST = 1;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var int
     *
     * Mode
     */
    private $mode;

    /**
     * @var string
     *
     * Query text
     */
    private $queryText;

    /**
     * @var string[]
     *
     * Applied filters
     */
    private $appliedFilters;

    /**
     * @var BoostClause[]
     *
     * Boost clauses
     */
    private $boostClauses = [];

    /**
     * @var null|DateTime
     *
     * From
     */
    private $from;

    /**
     * @var null|DateTime
     *
     * To
     */
    private $to;

    /**
     * @var bool
     *
     * Enabled
     */
    private $enabled;

    /**
     * Campaign constructor.
     *
     * @param string        $name
     * @param null|DateTime $from
     * @param null|DateTime $to
     */
    public function __construct(
        string $name,
        ?DateTime $from,
        ?DateTime $to
    ) {
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
        $this->enabled = false;
    }

    /**
     * Add boost clause.
     *
     * @param BoostClause $boostClause
     */
    public function addBoostClause(BoostClause $boostClause)
    {
        $this->boostClauses[] = $boostClause;
    }

    /**
     * Set environment.
     *
     * @param string   $queryText
     * @param string[] $appliedFilters
     */
    public function setEnvironment(
        string $queryText,
        array $appliedFilters
    ) {
        $this->queryText = $queryText;
        $this->appliedFilters = $appliedFilters;
    }

    /**
     * @param int $mode
     */
    public function setMode(int $mode)
    {
        $this->mode = $mode;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get mode.
     *
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * Get query text.
     *
     * @return string
     */
    public function getQueryText(): string
    {
        return $this->queryText;
    }

    /**
     * Get applied filters.
     *
     * @return string[]
     */
    public function getAppliedFilters(): array
    {
        return $this->appliedFilters;
    }

    /**
     * Get boost clauses.
     *
     * @return BoostClause[]
     */
    public function getBoostClauses(): array
    {
        return $this->boostClauses;
    }

    /**
     * Get from.
     *
     * @return DateTime|null
     */
    public function getFrom(): ? DateTime
    {
        return $this->from;
    }

    /**
     * Get to.
     *
     * @return DateTime|null
     */
    public function getTo(): ? DateTime
    {
        return $this->to;
    }

    /**
     * Set enabled.
     *
     * @var bool
     */
    public function enable(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'mode' => $this->mode,
            'query_text' => $this->queryText,
            'applied_filters' => $this->appliedFilters,
            'boost_clauses' => array_map(function (BoostClause $boostClause) {
                return $boostClause->toArray();
            }, $this->boostClauses),
            'from' => is_null($this->from)
                ? null
                : $this->from->format('U'),
            'to' => is_null($this->to)
                ? null
                : $this->to->format('U'),
            'enabled' => $this->enabled,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        if (!isset($array['name'])) {
            throw InvalidFormatException::campaignFormatNotValid(json_encode($array));
        }

        $campaign = new self(
            $array['name'],
            isset($array['from'])
                ? Carbon::createFromTimestampUTC($array['from'])
                : null,
            isset($array['to'])
                ? Carbon::createFromTimestampUTC($array['to'])
                : null
        );

        $campaign->setEnvironment(
            $array['query_text'] ?? '',
            $array['applied_filters'] ?? []
        );

        $campaign->mode = $array['mode'] ?? self::MODE_BOOST;
        $campaign->enabled = $array['enabled'] ?? false;
        $campaign->boostClauses = array_map(function (array $boostClause) {
            return BoostClause::createFromArray($boostClause);
        }, ($array['boost_clauses'] ?? []));

        return $campaign;
    }
}
