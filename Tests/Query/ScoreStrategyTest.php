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

use Apisearch\Query\ScoreStrategy;
use PHPUnit_Framework_TestCase;

/**
 * Class ScoreStrategyTest.
 */
class ScoreStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create default.
     */
    public function testCreateDefault()
    {
        $scoreStrategy = ScoreStrategy::createDefault();
        $this->assertEquals(
            ScoreStrategy::DEFAULT,
            $scoreStrategy->getType()
        );
        $this->assertNull($scoreStrategy->getFunction());
    }

    /**
     * Test create relevance boosting.
     */
    public function testCreateRelevanceBoosting()
    {
        $scoreStrategy = ScoreStrategy::createRelevanceBoosting();
        $this->assertEquals(
            ScoreStrategy::BOOSTING_RELEVANCE_FIELD,
            $scoreStrategy->getType()
        );
        $this->assertNull($scoreStrategy->getFunction());
    }

    /**
     * Test create default.
     */
    public function testCreateCustomFunction()
    {
        $function = 'xxx';
        $scoreStrategy = ScoreStrategy::createCustomFunction($function);
        $this->assertEquals(
            ScoreStrategy::CUSTOM_FUNCTION,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            $function,
            $scoreStrategy->getFunction()
        );
    }

    /**
     * Test http transportable methods.
     */
    public function testHttpTransportableMethods()
    {
        $function = 'xxx';
        $array = [
            'type' => ScoreStrategy::CUSTOM_FUNCTION,
            'function' => $function,
        ];
        $scoreStrategy = ScoreStrategy::createFromArray($array);
        $this->assertEquals(
            ScoreStrategy::CUSTOM_FUNCTION,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            $function,
            $scoreStrategy->getFunction()
        );
        $this->assertEquals(
            $array,
            $scoreStrategy->toArray()
        );
    }
}
