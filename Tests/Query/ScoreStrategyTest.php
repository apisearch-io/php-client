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
use Apisearch\Query\ScoreStrategy;
use Apisearch\Tests\HttpHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class ScoreStrategyTest.
 */
class ScoreStrategyTest extends TestCase
{
    /**
     * Test create default.
     */
    public function testCreateDefault()
    {
        $scoreStrategy = ScoreStrategy::createDefault();
        $this->assertEquals(
            ScoreStrategy::DEFAULT_TYPE,
            $scoreStrategy->getType()
        );
        $this->assertNull($scoreStrategy->getFilter());
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            ScoreStrategy::DEFAULT_TYPE,
            $scoreStrategy->getType()
        );
        $this->assertNull($scoreStrategy->getFilter());
    }

    /**
     * Test create field boosting.
     */
    public function testFieldRelevanceBoosting()
    {
        $scoreStrategy = ScoreStrategy::createFieldBoosting('relevance');
        $this->assertEquals(
            ScoreStrategy::BOOSTING_FIELD_VALUE,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            'relevance',
            $scoreStrategy->getConfigurationValue('field')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_FACTOR,
            $scoreStrategy->getConfigurationValue('factor')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_MISSING,
            $scoreStrategy->getConfigurationValue('missing')
        );
        $this->assertEquals(
            ScoreStrategy::MODIFIER_NONE,
            $scoreStrategy->getConfigurationValue('modifier')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertNull($scoreStrategy->getFilter());
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            ScoreStrategy::BOOSTING_FIELD_VALUE,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            'relevance',
            $scoreStrategy->getConfigurationValue('field')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_FACTOR,
            $scoreStrategy->getConfigurationValue('factor')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_MISSING,
            $scoreStrategy->getConfigurationValue('missing')
        );
        $this->assertEquals(
            ScoreStrategy::MODIFIER_NONE,
            $scoreStrategy->getConfigurationValue('modifier')
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertNull($scoreStrategy->getFilter());

        $scoreStrategy = ScoreStrategy::createFieldBoosting(
            'relevance',
            1.0,
            2.0,
            ScoreStrategy::MODIFIER_LN,
            4.00,
            Filter::create('x', [], 0, '')
        );
        $this->assertEquals(
            1.0,
            $scoreStrategy->getConfigurationValue('factor')
        );
        $this->assertEquals(
            2.0,
            $scoreStrategy->getConfigurationValue('missing')
        );
        $this->assertEquals(
            ScoreStrategy::MODIFIER_LN,
            $scoreStrategy->getConfigurationValue('modifier')
        );
        $this->assertEquals(
            4.0,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            1.0,
            $scoreStrategy->getConfigurationValue('factor')
        );
        $this->assertEquals(
            2.0,
            $scoreStrategy->getConfigurationValue('missing')
        );
        $this->assertEquals(
            ScoreStrategy::MODIFIER_LN,
            $scoreStrategy->getConfigurationValue('modifier')
        );
        $this->assertEquals(
            4.0,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
    }

    /**
     * Test custom function.
     */
    public function testCustomFunction()
    {
        $scoreStrategy = ScoreStrategy::createCustomFunction(
            'xxx'
        );
        $this->assertEquals(
            ScoreStrategy::CUSTOM_FUNCTION,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertEquals(
            'xxx',
            $scoreStrategy->getConfigurationValue('function')
        );
        $this->assertNull($scoreStrategy->getFilter());
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            ScoreStrategy::CUSTOM_FUNCTION,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertEquals(
            'xxx',
            $scoreStrategy->getConfigurationValue('function')
        );
        $this->assertNull($scoreStrategy->getFilter());

        /**
         * Test with filter.
         */
        $scoreStrategy = ScoreStrategy::createCustomFunction(
            'xxx',
            2.34,
            Filter::create('x', [], 0, '')
        );
        $this->assertEquals(
            2.34,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            2.34,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
    }

    /**
     * Test decay.
     */
    public function testDecay()
    {
        $scoreStrategy = ScoreStrategy::createDecayFunction(
            ScoreStrategy::DECAY_GAUSS,
            'field',
            '1m',
            'scale',
            '10',
            1.0
        );
        $this->assertEquals(
            ScoreStrategy::DECAY,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertEquals(
            ScoreStrategy::DECAY_GAUSS,
            $scoreStrategy->getConfigurationValue('type')
        );
        $this->assertEquals(
            'field',
            $scoreStrategy->getConfigurationValue('field')
        );
        $this->assertEquals(
            '1m',
            $scoreStrategy->getConfigurationValue('origin')
        );
        $this->assertEquals(
            'scale',
            $scoreStrategy->getConfigurationValue('scale')
        );
        $this->assertEquals(
            '10',
            $scoreStrategy->getConfigurationValue('offset')
        );
        $this->assertEquals(
            1.0,
            $scoreStrategy->getConfigurationValue('decay')
        );
        $this->assertNull($scoreStrategy->getFilter());
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            ScoreStrategy::DECAY,
            $scoreStrategy->getType()
        );
        $this->assertEquals(
            ScoreStrategy::DEFAULT_WEIGHT,
            $scoreStrategy->getWeight()
        );
        $this->assertEquals(
            ScoreStrategy::DECAY_GAUSS,
            $scoreStrategy->getConfigurationValue('type')
        );
        $this->assertEquals(
            'field',
            $scoreStrategy->getConfigurationValue('field')
        );
        $this->assertEquals(
            '1m',
            $scoreStrategy->getConfigurationValue('origin')
        );
        $this->assertEquals(
            'scale',
            $scoreStrategy->getConfigurationValue('scale')
        );
        $this->assertEquals(
            '10',
            $scoreStrategy->getConfigurationValue('offset')
        );
        $this->assertEquals(
            1.0,
            $scoreStrategy->getConfigurationValue('decay')
        );
        $this->assertNull($scoreStrategy->getFilter());

        /**
         * Test with filter.
         */
        $scoreStrategy = ScoreStrategy::createDecayFunction(
            ScoreStrategy::DECAY_GAUSS,
            'field',
            '1m',
            'scale',
            '10',
            1.0,
            5.50,
            Filter::create('x', [], 0, '')
        );
        $this->assertEquals(
            5.50,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertEquals(
            5.50,
            $scoreStrategy->getWeight()
        );
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
        $scoreStrategy = HttpHelper::emulateHttpTransport($scoreStrategy);
        $this->assertInstanceOf(
            Filter::class,
            $scoreStrategy->getFilter()
        );
    }
}
