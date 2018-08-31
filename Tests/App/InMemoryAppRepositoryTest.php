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

use Apisearch\App\InMemoryAppRepository;
use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * File header placeholder.
 */
class InMemoryAppRepositoryTest extends TestCase
{
    /**
     * Test create index.
     */
    public function testCreateIndex()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test create index already created.
     *
     * @expectedException \Apisearch\Exception\ResourceExistsException
     */
    public function testCreateIndexAlreadyCreated()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
    }

    /**
     * Test delete index.
     */
    public function testDeleteIndex()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $this->assertCount(0, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test delete index.
     *
     * @expectedException \Apisearch\Exception\ResourceNotAvailableException
     */
    public function testDeleteIndexAlreadyDeleted()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
    }

    /**
     * Test delete non existing index.
     *
     * @expectedException \Apisearch\Exception\ResourceNotAvailableException
     */
    public function testDeleteNotExistingIndex()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->resetIndex(IndexUUID::createById('yyy'));
    }

    /**
     * Test configure non existing index.
     *
     * @expectedException \Apisearch\Exception\ResourceNotAvailableException
     */
    public function testConfigureNotExistingIndex()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->configureIndex(IndexUUID::createById('yyy'), Config::createFromArray([]));
    }

    /**
     * Test check index.
     */
    public function testCheckIndex()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $this->assertFalse($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $this->assertTrue($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
        $inMemoryAppRepository->deleteIndex(IndexUUID::createById('yyy'));
        $this->assertFalse($inMemoryAppRepository->checkIndex(IndexUUID::createById('yyy')));
    }

    /**
     * Test get indices.
     */
    public function testGetIndices()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $this->assertEquals('yyy', $inMemoryAppRepository->getIndices()[0]->getUUID()->getId());
        $this->assertEquals('xxx', $inMemoryAppRepository->getIndices()[0]->getAppUUID()->composeUUID());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('zzz'), new ImmutableConfig());
        $this->assertCount(2, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('ooo')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('aaa'), new ImmutableConfig());
        $this->assertCount(1, $inMemoryAppRepository->getIndices());
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create());
        $this->assertCount(3, $inMemoryAppRepository->getIndices());
    }

    /**
     * Test add Token.
     */
    public function testAddToken()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->addToken($token);
        $inMemoryAppRepository->addToken($token);
        $this->assertCount(1, $inMemoryAppRepository->getTokens());
        $token2 = new Token(TokenUUID::createById('uuu'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->addToken($token2);
        $this->assertCount(2, $inMemoryAppRepository->getTokens());
    }

    /**
     * Test delete Token.
     */
    public function testDeleteToken()
    {
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->addToken($token);
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
        $inMemoryAppRepository = new InMemoryAppRepository();
        $inMemoryAppRepository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx')));
        $inMemoryAppRepository->createIndex(IndexUUID::createById('yyy'), new ImmutableConfig());
        $token = new Token(TokenUUID::createById('lll'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->addToken($token);
        $token2 = new Token(TokenUUID::createById('uuu'), AppUUID::createById('xxx'));
        $inMemoryAppRepository->addToken($token2);
        $this->assertCount(2, $inMemoryAppRepository->getTokens());
        $inMemoryAppRepository->deleteTokens();
        $this->assertCount(0, $inMemoryAppRepository->getTokens());
    }
}
