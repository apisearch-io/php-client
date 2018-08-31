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

use Apisearch\Model\IndexUUID;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexUUIDTest.
 */
class IndexUUIDTest extends TestCase
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
        IndexUUID::createFromArray($data);
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
    public function testCreateValidIndexUUID(): void
    {
        $indexUUID = IndexUUID::createFromArray([
            'id' => 'testId',
        ]);
        $this->assertEquals('testId', $indexUUID->getId());

        $indexUUID2 = IndexUUID::createById('testId');
        $this->assertEquals('testId', $indexUUID2->getId());
        $this->assertEquals('testId', $indexUUID2->toArray()['id']);
        $this->assertEquals('testId', $indexUUID2->composeUUID());
    }
}
