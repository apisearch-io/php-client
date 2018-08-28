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

use Apisearch\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    /**
     * Test user creation.
     */
    public function testCreateUser()
    {
        $user = new User('1234');
        $this->assertEquals('1234', $user->getId());
        $this->assertEmpty($user->getAttributes());

        $user = new User('1234', ['field1' => 'value1']);
        $this->assertEquals('1234', $user->getId());
        $this->assertEquals(['field1' => 'value1'], $user->getAttributes());
    }

    /**
     * Test http transport.
     */
    public function testHttpTransport()
    {
        $user = new User('1234', ['field1' => 'value1']);
        $user = $user->toArray();
        $this->assertEquals(
            $user,
            User::createFromArray($user)->toArray()
        );
    }

    /**
     * Test create from array with exception.
     *
     * @dataProvider dataCreateFromArrayException
     *
     * @expectedException \Apisearch\Exception\InvalidFormatException
     */
    public function testCreateFromArrayException(array $user)
    {
        User::createFromArray($user);
    }

    /**
     * Data for testCreateByComposedUUIDException.
     */
    public function dataCreateFromArrayException()
    {
        return [
            [[]],
            [['attributes' => []]],
        ];
    }
}
