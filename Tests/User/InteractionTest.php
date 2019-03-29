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

namespace Apisearch\Tests\User;

use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;
use Apisearch\Tests\HttpHelper;
use Apisearch\User\Interaction;
use PHPUnit\Framework\TestCase;

/**
 * Class InteractionTest.
 */
class InteractionTest extends TestCase
{
    /**
     * Test http transport.
     */
    public function testHttpTransport()
    {
        $interaction = new Interaction(
            new User('123', ['a' => 1]),
            new ItemUUID('abc', 'article'),
            'buy'
        );

        $this->assertEquals(
            $interaction,
            HttpHelper::emulateHttpTransport($interaction)
        );

        $this->assertEquals('123', $interaction->getUser()->getId());
        $this->assertEquals('abc', $interaction->getItemUUID()->getId());
        $this->assertEquals('buy', $interaction->getEventName());
        $this->assertEquals([], $interaction->getMetadata());
    }

    /**
     * Test http transport defaults.
     */
    public function testHttpTransportDefaults()
    {
        $interaction = new Interaction(
            new User('123', ['a' => 1]),
            new ItemUUID('abc', 'article'),
            'buy',
            ['field' => 'value']
        );

        $this->assertEquals(
            $interaction,
            HttpHelper::emulateHttpTransport($interaction)
        );
        $this->assertEquals(['field' => 'value'], $interaction->getMetadata());
    }
}
