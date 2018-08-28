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

namespace Apisearch\Tests\Repository;

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * Class RepositoryReferenceTest.
 */
class RepositoryReferenceTest extends TestCase
{
    /**
     * Test repository reference.
     */
    public function testRepositoryReference()
    {
        $repositoryReference = RepositoryReference::create(
            AppUUID::createById('123'),
            IndexUUID::createById('456')
        );

        $this->assertEquals(
            $repositoryReference->getAppUUID()->getId(),
            '123'
        );

        $this->assertEquals(
            $repositoryReference->getIndexUUID()->getId(),
            '456'
        );

        $this->assertEquals(
            '123_456',
            $repositoryReference->compose()
        );
    }

    /**
     * Test repository reference no index.
     */
    public function testRepositoryReferenceNoIndex()
    {
        $repositoryReference = RepositoryReference::create(
            AppUUID::createById('123')
        );

        $this->assertEquals(
            $repositoryReference->getAppUUID()->getId(),
            '123'
        );

        $this->assertNull(
            $repositoryReference->getIndexUUID()
        );

        $this->assertEquals(
            '123_',
            $repositoryReference->compose()
        );
    }

    /**
     * Test repository reference no app nor index.
     */
    public function testRepositoryReferenceNoAppNorIndex()
    {
        $repositoryReference = RepositoryReference::create();

        $this->assertNull(
            $repositoryReference->getAppUUID()
        );

        $this->assertNull(
            $repositoryReference->getIndexUUID()
        );

        $this->assertEquals(
            '_',
            $repositoryReference->compose()
        );
    }

    /**
     * Test change index.
     */
    public function testChangeIndex()
    {
        $repositoryReference = RepositoryReference::create(
            AppUUID::createById('123'),
            IndexUUID::createById('456')
        );

        $newRepositoryReference = $repositoryReference->changeIndex(IndexUUID::createById('999'));
        $this->assertEquals(
            '456',
            $repositoryReference->getIndexUUID()->getId()
        );

        $this->assertEquals(
            '999',
            $newRepositoryReference->getIndexUUID()->getId()
        );
    }

    /**
     * Test bad ids.
     *
     * @expectedException \Apisearch\Exception\InvalidFormatException
     */
    public function testBadIds()
    {
        RepositoryReference::create(
            AppUUID::createById('1_2_3'),
            IndexUUID::createById('4_5_6')
        );
    }

    /**
     * Test create from composed.
     */
    public function testCreateFromComposed()
    {
        $repositoryReference = RepositoryReference::createFromComposed('1-2-3_4-5-6');
        $this->assertEquals(
            $repositoryReference->getAppUUID()->getId(),
            '1-2-3'
        );

        $this->assertEquals(
            $repositoryReference->getIndexUUID()->getId(),
            '4-5-6'
        );
    }
}
