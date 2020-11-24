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

namespace Apisearch\Tests\App;

use Apisearch\App\AppRepository;
use Apisearch\Config\Config;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * Class AppRepositoryTest.
 */
abstract class AppRepositoryTest extends TestCase
{
    /**
     * Get repository intance.
     *
     * @return AppRepository
     */
    abstract protected function getRepository(): AppRepository;

    /**
     * Test create index.
     */
    public function testCreateIndex()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test create index already created.
     */
    public function testCreateIndexAlreadyCreated()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $this->expectException(ResourceExistsException::class);
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
    }

    /**
     * Test delete index.
     */
    public function testDeleteIndex()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $this->assertCount(0, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test delete index.
     */
    public function testDeleteIndexAlreadyDeleted()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $this->expectException(ResourceNotAvailableException::class);
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
    }

    /**
     * Test delete non existing index.
     */
    public function testDeleteNotExistingIndex()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $this->expectException(ResourceNotAvailableException::class);
        $inMemoryAppRepository->resetIndex(IndexUUID::createById('yyy'));
    }

    /**
     * Test configure non existing index.
     */
    public function testConfigureNotExistingIndex()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $this->expectException(ResourceNotAvailableException::class);
        $inMemoryAppRepository->configureIndex(IndexUUID::createById('yyy'), Config::createFromArray([]));
    }

    /**
     * Test check index.
     */
    public function testCheckIndex()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $this->assertFalse($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $this->assertTrue($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $this->assertFalse($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
    }

    /**
     * Test get indices.
     */
    public function testGetIndices()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $this->assertEquals('yyy', $inMemoryAppRepository->getIndices()[0]->getUUID()->getId());
        $this->assertEquals('xxx', $inMemoryAppRepository->getIndices()[0]->getAppUUID()->composeUUID());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('zzz'), new Config());
        $this->assertCount(2, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('ooo')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('aaa'), new Config());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create());
        $this->assertCount(3, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test add Token.
     */
    public function testAddToken()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->putToken($token);
        $inMemoryAppRepository->putToken($token);
        $this->assertCount(1, $inMemoryAppRepository->getTokens());
        $token2 = new Token(TokenUUID::createById('uuu'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->putToken($token2);
        $this->assertCount(2, $inMemoryAppRepository->getTokens());
    }

    /**
     * Test delete Token.
     */
    public function testDeleteToken()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->putToken($token);
        $this->assertCount(1, $inMemoryAppRepository->getTokens());
        $inMemoryAppRepository->deleteToken(TokenUUID::createById('nono'));
        $this->assertCount(1, $inMemoryAppRepository->getTokens());
        $inMemoryAppRepository->deleteToken(TokenUUID::createById('lll'));
        $this->assertCount(0, $inMemoryAppRepository->getTokens());
    }

    /**
     * Test delete tokens.
     */
    public function testDeleteTokens()
    {
        $inMemoryAppRepository = $this->getRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new Config());
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->putToken($token);
        $token2 = new Token(TokenUUID::createById('uuu'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->putToken($token2);
        $this->assertCount(2, $inMemoryAppRepository->getTokens());
        $inMemoryAppRepository->deleteTokens();
        $this->assertCount(0, $inMemoryAppRepository->getTokens());
    }
}
