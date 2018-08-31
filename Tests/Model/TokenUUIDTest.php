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

use Apisearch\Model\TokenUUID;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenUUIDTest.
 */
class TokenUUIDTest extends TestCase
{
    /**
     * Test creation with bad data.
     *
     * @dataProvider dataEmptyCreation
     *
     * @expectedException \Apisearch\Exception\InvalidFormatException
     */
    public function testEmptyCreation(array $data): void
    {
        TokenUUID::createFromArray($data);
    }

    /**
     * Data for testEmptyCreation.
     */
    public function dataEmptyCreation(): array
    {
        return [
            [[]],
            [['another' => '123']],
        ];
    }

    /**
     * Test add change.
     */
    public function testCreateValidTokenUUID(): void
    {
        $tokenUUID = TokenUUID::createFromArray([
            'id' => 'testId',
        ]);
        $this->assertEquals('testId', $tokenUUID->getId());

        $tokenUUID2 = TokenUUID::createById('testId');
        $this->assertEquals('testId', $tokenUUID2->getId());
        $this->assertEquals('testId', $tokenUUID2->toArray()['id']);
        $this->assertEquals('testId', $tokenUUID2->composeUUID());
    }
}
