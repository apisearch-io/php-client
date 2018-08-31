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
use PHPUnit\Framework\TestCase;

/**
 * Class AppUUIDTest.
 */
class AppUUIDTest extends TestCase
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
        AppUUID::createFromArray($data);
    }

    /**
     * Data for testEmptyCreation.
     */
    public function dataEmptyCreation(): array
    {
        return [
            [[]],
            [['another' => '123']],
            [['id' => 'a_b']],
        ];
    }

    /**
     * Test add change.
     */
    public function testCreateValidAppUUID(): void
    {
        $appUUID = AppUUID::createFromArray([
            'id' => 'testId',
        ]);
        $this->assertEquals('testId', $appUUID->getId());

        $appUUID2 = AppUUID::createById('testId');
        $this->assertEquals('testId', $appUUID2->getId());
        $this->assertEquals('testId', $appUUID2->toArray()['id']);
        $this->assertEquals('testId', $appUUID2->composeUUID());
    }
}
