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
            'shards' => 3,
            'replicas' => 4,
            'fields' => [
                'hello' => 'string',
            ],
            'metadata' => ['hello'],
        ]);

        $this->assertEquals('testId', $index1->getUUID()->getId());
        $this->assertEquals('testAppId', $index1->getAppUUID()->composeUUID());
        $this->assertTrue($index1->isOK());
        $this->assertEquals(10, $index1->getDocCount());
        $this->assertEquals('1kb', $index1->getSize());
        $this->assertEquals(3, $index1->getShards());
        $this->assertEquals(4, $index1->getReplicas());
        $this->assertEquals(['hello'], $index1->getMetadata());
        $this->assertEquals(['hello' => 'string'], $index1->getFields());

        $index2 = new Index(IndexUUID::createById('testId'), AppUUID::createById('testAppId'), true, 20, '2kb', 3, 4, ['hello' => 'string'], ['hello']);
        $this->assertEquals('testId', $index2->getUUID()->getId());
        $this->assertEquals('testAppId', $index2->getAppUUID()->composeUUID());
        $this->assertTrue($index2->isOK());
        $this->assertEquals(20, $index2->getDocCount());
        $this->assertEquals('2kb', $index2->getSize());
        $this->assertEquals(3, $index2->getShards());
        $this->assertEquals(4, $index2->getReplicas());
        $this->assertEquals(['hello' => 'string'], $index2->getFields());
        $this->assertEquals(['hello'], $index2->getMetadata());

        $this->assertEquals('testId', $index2->toArray()['uuid']['id']);
        $this->assertEquals('testAppId', $index2->toArray()['app_id']['id']);
        $this->assertTrue($index2->toArray()['is_ok']);
        $this->assertEquals(20, $index2->toArray()['doc_count']);
        $this->assertEquals('2kb', $index2->toArray()['size']);
        $this->assertEquals(3, $index2->toArray()['shards']);
        $this->assertEquals(4, $index2->toArray()['replicas']);
        $this->assertEquals(['hello'], $index2->toArray()['metadata']);

        $index3 = Index::createFromArray([
            'uuid' => [
                'id' => 'testId',
            ],
            'app_id' => [
                'id' => 'testAppId',
            ],
            'shards' => 9,
            'replicas' => 8,
        ]);
        $this->assertFalse($index3->toArray()['is_ok']);
        $this->assertEquals(0, $index3->toArray()['doc_count']);
        $this->assertEquals('0kb', $index3->toArray()['size']);
        $this->assertEquals(9, $index3->getShards());
        $this->assertEquals(8, $index3->getReplicas());
        $this->assertEquals([], $index3->getMetadata());
        $this->assertEquals([], $index3->getFields());
    }

    /**
     * Test with metadata value.
     */
    public function testWithMetadataValue()
    {
        $index = Index::createFromArray([
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

        $this->assertNull($index->getMetadataValue('key1'));
        $index->withMetadataValue('key1', 'val1');
        $this->assertEquals('val1', $index->getMetadataValue('key1'));
        $index = Index::createFromArray($index->toArray());
        $this->assertEquals('val1', $index->getMetadataValue('key1'));
    }
}
