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

namespace Apisearch\Query;

use Apisearch\Model\HttpTransportable;

/**
 * Class ScoreStrategies.
 */
class ScoreStrategies implements HttpTransportable
{
    /**
     * @var string
     *
     * Score mode multiply
     */
    const MULTIPLY = 'multiply';

    /**
     * @var string
     *
     * Score mode sum
     */
    const SUM = 'sum';

    /**
     * @var string
     *
     * Score mode avg
     */
    const AVG = 'avg';

    /**
     * @var string
     *
     * Score mode max
     */
    const MAX = 'max';

    /**
     * @var string
     *
     * Score mode min
     */
    const MIN = 'min';

    /**
     * @var ScoreStrategy[]
     *
     * Score strategy array
     */
    private $scoreStrategies = [];

    /**
     * @var string
     *
     * Scoring type
     */
    private $scoreMode;

    /**
     * Create empty.
     *
     * @param string $scoreMode
     *
     * @return self
     */
    public static function createEmpty(string $scoreMode = self::SUM): self
    {
        $scoreStrategies = new self();
        $scoreStrategies->scoreMode = $scoreMode;

        return $scoreStrategies;
    }

    /**
     * Add Score Strategy.
     *
     * @param ScoreStrategy $scoreStrategy
     *
     * @return ScoreStrategies
     */
    public function addScoreStrategy(ScoreStrategy $scoreStrategy): ScoreStrategies
    {
        $this->scoreStrategies[] = $scoreStrategy;

        return $this;
    }

    /**
     * Get score strategies.
     *
     * @return ScoreStrategy[]
     */
    public function getScoreStrategies(): array
    {
        return $this->scoreStrategies;
    }

    /**
     * Get score mode.
     *
     * @return string
     */
    public function getScoreMode(): string
    {
        return $this->scoreMode;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'score_mode' => $this->scoreMode,
            'score_strategies' => array_map(function (ScoreStrategy $scoreStrategy) {
                return $scoreStrategy->toArray();
            }, $this->scoreStrategies),
        ];
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
        $scoreStrategies = new self();
        $scoreStrategies->scoreMode = $array['score_mode'] ?: self::SUM;
        $scoreStrategies->scoreStrategies = array_map(function (array $scoreStrategy) {
            return ScoreStrategy::createFromArray($scoreStrategy);
        }, $array['score_strategies']);

        return $scoreStrategies;
    }
}
