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

namespace Apisearch\App;

use Apisearch\Config\Config;
use Apisearch\Exception\MockException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;

/**
 * Class MockAppRepository.
 */
class MockAppRepository extends HttpRepositoryWithCredentials implements AppRepository
{
    /**
     * Add token.
     *
     * @param Token $token
     */
    public function putToken(Token $token)
    {
        $this->throwMockException();
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        $this->throwMockException();
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        $this->throwMockException();
    }

    /**
     * Purge tokens.
     */
    public function deleteTokens()
    {
        $this->throwMockException();
    }

    /**
     * Get indices.
     *
     * @return Index[]
     */
    public function getIndices(): array
    {
        $this->throwMockException();
    }

    /**
     * Create an index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        IndexUUID $indexUUID,
        Config $config
    ) {
        $this->throwMockException();
    }

    /**
     * Delete an index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex(IndexUUID $indexUUID)
    {
        $this->throwMockException();
    }

    /**
     * Reset the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex(IndexUUID $indexUUID)
    {
        $this->throwMockException();
    }

    /**
     * Checks the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @return bool
     */
    public function checkIndex(IndexUUID $indexUUID): bool
    {
        $this->throwMockException();
    }

    /**
     * Config the index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     * @param bool      $forceReindex
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        IndexUUID $indexUUID,
        Config $config,
        bool $forceReindex = false
    ) {
        $this->throwMockException();
    }

    /**
     * Throw exception.
     *
     * @throws MockException
     */
    private function throwMockException()
    {
        throw MockException::isAMock();
    }
}
