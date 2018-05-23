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

namespace Apisearch\Query;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * Class ScoreStrategy.
 */
class ScoreStrategy implements HttpTransportable
{
    /**
     * @var int
     *
     * Default score strategy
     */
    const DEFAULT = 0;

    /**
     * @var int
     *
     * Boosting by relevance field
     */
    const BOOSTING_RELEVANCE_FIELD = 1;

    /**
     * @var int
     *
     * Boosting by relevance field
     */
    const CUSTOM_FUNCTION = 2;

    /**
     * @var int
     *
     * Scoring type
     */
    private $type = self::DEFAULT;

    /**
     * @var null|string
     *
     * Scoring function
     */
    private $function = null;

    /**
     * Get type.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Get function.
     *
     * @return null|string
     */
    public function getFunction(): ? string
    {
        return $this->function;
    }

    /**
     * Create empty.
     *
     * @return self
     */
    public static function createDefault(): self
    {
        return new self();
    }

    /**
     * Create default relevance scoring.
     *
     * @return self
     */
    public static function createRelevanceBoosting(): self
    {
        $score = self::createDefault();
        $score->type = self::BOOSTING_RELEVANCE_FIELD;

        return $score;
    }

    /**
     * Create custom function scoring.
     *
     * @param string $function
     *
     * @return self
     */
    public static function createCustomFunction(string $function): self
    {
        $score = self::createDefault();
        $score->type = self::CUSTOM_FUNCTION;
        $score->function = $function;

        return $score;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'function' => $this->function,
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
        $score = new self();
        $score->type = $array['type'] ?: self::DEFAULT;
        $score->function = $array['function'] ?: null;

        return $score;
    }
}
