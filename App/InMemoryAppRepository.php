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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Repository\RepositoryWithCredentials;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class InMemoryAppRepository.
 */
class InMemoryAppRepository extends RepositoryWithCredentials implements AppRepository
{
    /**
     * @var array
     *
     * Items
     */
    private $tokens = [];

    /**
     * Get index position by credentials.
     *
     * @return string
     */
    private function getIndexKey(): string
    {
        return $this
            ->getRepositoryReference()
            ->getAppId();
    }

    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        if (!array_key_exists($this->getIndexKey(), $this->tokens)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $this->tokens[$this->getIndexKey()][$token->getTokenUUID()->composeUUID()] = $token;
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        if (!array_key_exists($this->getIndexKey(), $this->tokens)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        unset($this->tokens[$this->getIndexKey()][$tokenUUID->composeUUID()]);
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        if (!array_key_exists($this->getIndexKey(), $this->tokens)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        return $this->tokens[$this->getIndexKey()];
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        if (!array_key_exists($this->getIndexKey(), $this->tokens)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $this->tokens[$this->getIndexKey()] = [];
    }
}
