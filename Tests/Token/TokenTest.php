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

namespace Apisearch\Tests\Token;

use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;
use PHPUnit_Framework_TestCase;

/**
 * File header placeholder.
 */
class TokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test token to and from array.
     */
    public function testToFromArray()
    {
        $token = new Token(
            TokenUUID::createById('1234'),
            '987',
            ['index1', 'index2'],
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
