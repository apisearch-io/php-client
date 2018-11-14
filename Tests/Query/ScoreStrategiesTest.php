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
}
