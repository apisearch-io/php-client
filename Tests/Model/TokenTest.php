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

namespace Apisearch\Tests\Model;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use PHPUnit\Framework\TestCase;

/**
 * File header placeholder.
 */
class TokenTest extends TestCase
{
    /**
     * Test token to and from array.
     */
    public function testToFromArray()
    {
        $token = new Token(
            TokenUUID::createById('1234'),
            AppUUID::createById('987'),
            [IndexUUID::createById('index1'), IndexUUID::createById('index2')],
            ['referrer1', 'referrer2'],
            ['get~~endpoint1', 'post~~endpoint2'],
            ['plugin1', 'plugin2'],
            4,
            3,
            3600
        );

        $this->assertEquals(
            $token,
            Token::createFromArray($token->toArray())
        );
    }
}
