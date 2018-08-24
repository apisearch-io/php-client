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

use Apisearch\Model\Index;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexTest.
 */
class IndexTest extends TestCase
{
    /**
     * @expectedException \Apisearch\Exception\InvalidFormatException
     */
    public function testEmptyCreation(): void
    {
        Index::createFromArray([]);
    }

    /**
     * Test add change.
     */
    public function testCreateValidIndex(): void
    {
        $index1 = Index::createFromArray(
            [
                'app_id' => 'testAppId',
                'name' => 'testName',
                'doc_count' => 10,
            ]);
        $this->assertEquals('testAppId', $index1->getAppId());
        $this->assertEquals('testName', $index1->getName());
        $this->assertEquals(10, $index1->getDocCount());

        $index2 = new Index('testAppId', 'testName', 10);
        $this->assertEquals('testAppId', $index2->getAppId());
        $this->assertEquals('testName', $index2->getName());
        $this->assertEquals(10, $index2->getDocCount());

        $this->assertEquals('testAppId', $index2->toArray()['app_id']);
        $this->assertEquals('testName', $index2->toArray()['name']);
        $this->assertEquals(10, $index2->toArray()['doc_count']);
    }
}
