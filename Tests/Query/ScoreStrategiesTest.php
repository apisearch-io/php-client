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

namespace Apisearch\Tests\Query;

use Apisearch\Query\Filter;
use Apisearch\Query\ScoreStrategies;
use Apisearch\Query\ScoreStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class ScoreStrategiesTest.
 */
class ScoreStrategiesTest extends TestCase
{
    /**
     * Test empty.
     */
    public function testEmpty()
    {
        $scoreStrategies = ScoreStrategies::createEmpty();
        $this->assertEquals(
            ScoreStrategies::SUM,
            $scoreStrategies->getScoreMode()
        );
        $this->assertEquals(
            [],
            $scoreStrategies->getScoreStrategies()
        );
    }

    /**
     * Test empty.
     */
    public function testMultiple()
    {
        $scoreStrategies = ScoreStrategies::createEmpty(ScoreStrategies::AVG);
        $scoreStrategies
            ->addScoreStrategy(ScoreStrategy::createDefault())
            ->addScoreStrategy(ScoreStrategy::createDefault())
            ->addScoreStrategy(ScoreStrategy::createDefault());

        $this->assertEquals(
            ScoreStrategies::AVG,
            $scoreStrategies->getScoreMode()
        );
        $this->assertCount(
            3,
            $scoreStrategies->getScoreStrategies()
        );
    }

    /**
     * Test to array and from array.
     */
    public function testArrayTransformation()
    {
        $scoreStrategies = ScoreStrategies::createEmpty(ScoreStrategies::AVG);
        $scoreStrategies
            ->addScoreStrategy(ScoreStrategy::createDefault())
            ->addScoreStrategy(ScoreStrategy::createDefault())
            ->addScoreStrategy(ScoreStrategy::createWeightFunction(1.1, Filter::create('a', ['b'], Filter::MUST_ALL, Filter::TYPE_FIELD)))
            ->addScoreStrategy(ScoreStrategy::createFieldBoosting('field1', 1.1, 2.2, 'avg', 4.4, Filter::create('a2', ['b2'], Filter::MUST_ALL, Filter::TYPE_FIELD), 'none'))
            ->addScoreStrategy(ScoreStrategy::createDecayFunction('gauss', 'a1', 'a2', 'a3', 'a4', 1.1, 2.2, Filter::create('a3', ['b3'], Filter::MUST_ALL, Filter::TYPE_FIELD), 'kk'));
        $scoreStrategiesAsArray = $scoreStrategies->toArray();
        $this->assertCount(5, $scoreStrategies->getScoreStrategies());
        $covertedScoreStrategies = ScoreStrategies::createFromArray($scoreStrategiesAsArray);

        $this->assertEquals(
            $scoreStrategiesAsArray,
            $covertedScoreStrategies->toArray()
        );

        $this->assertCount(5, $covertedScoreStrategies->getScoreStrategies());
    }
}
