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
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest.
 */
class IndexTest extends TestCase
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
        Index::createFromArray($data);
    }

    /**
     * Data for testEmptyCreation.
     */
    public function dataEmptyCreation(): array
    {
        return [
            [[]],
            [['id' => '1234']],
            [['app_id' => '1234']],
            [['id' => '1234', 'doc_count' => 1]],
        ];
    }

    /**
     * Test add change.
     */
    public function testCreateValidIndex(): void
    {
        $index1 = Index::createFromArray([
            'uuid' => [
                'id' => 'testId',
            ],
            'app_id' => [
                'id' => 'testAppId',
            ],
            'is_ok' => true,
            'doc_count' => 10,
            'size' => '1kb',
        ]);

        $this->assertEquals('testId', $index1->getUUID()->getId());
        $this->assertEquals('testAppId', $index1->getAppUUID()->composeUUID());
        $this->assertTrue($index1->isOK());
        $this->assertEquals(10, $index1->getDocCount());
        $this->assertEquals('1kb', $index1->getSize());

        $index2 = new Index(IndexUUID::createById('testId'), AppUUID::createById('testAppId'), true, 20, '2kb');
        $this->assertEquals('testId', $index2->getUUID()->getId());
        $this->assertEquals('testAppId', $index2->getAppUUID()->composeUUID());
        $this->assertTrue($index2->isOK());
        $this->assertEquals(20, $index2->getDocCount());
        $this->assertEquals('2kb', $index2->getSize());

        $this->assertEquals('testId', $index2->toArray()['uuid']['id']);
        $this->assertEquals('testAppId', $index2->toArray()['app_id']['id']);
        $this->assertTrue($index2->toArray()['is_ok']);
        $this->assertEquals(20, $index2->toArray()['doc_count']);
        $this->assertEquals('2kb', $index2->toArray()['size']);

        $index3 = Index::createFromArray([
            'uuid' => [
                'id' => 'testId',
            ],
            'app_id' => [
                'id' => 'testAppId',
            ],
        ]);
        $this->assertFalse($index3->toArray()['is_ok']);
        $this->assertEquals(0, $index3->toArray()['doc_count']);
        $this->assertEquals('0kb', $index3->toArray()['size']);
    }
}
