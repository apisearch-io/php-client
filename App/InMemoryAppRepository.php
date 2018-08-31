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
use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\AppUUID;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\RepositoryWithCredentials;

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
    public $indices = [];

    /**
     * @var Token[]
     *
     * Tokens
     */
    public $tokens = [];

    /**
     * Get indices.
     *
     * @return Index[]
     */
    public function getIndices(): array
    {
        $appId = $this->getAppKey();

        $result = [];
        foreach ($this->indices as $index => $_) {
            if (false !== preg_match("/(?P<app_id>[^_]+)\_(?P<id>[\S]+)/", $index, $matches)) {
                if (!empty($appId) && $matches['app_id'] !== $appId) {
                    continue;
                }

                $indexMeta = [
                    'uuid' => ['id' => $matches['id']],
                    'app_id' => ['id' => $matches['app_id']],
                    'doc_count' => 0,
                ];

                $result[] = Index::createFromArray($indexMeta);
            }
        }

        return $result;
    }

    /**
     * Create an index.
     *
     * @param IndexUUID       $indexUUID
     * @param ImmutableConfig $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(
        IndexUUID $indexUUID,
        ImmutableConfig $config
    ) {
        if (array_key_exists($this->getIndexKey($indexUUID), $this->indices)) {
            throw ResourceExistsException::indexExists();
        }

        $this->indices[$this->getIndexKey($indexUUID)] = [
            'config' => $config,
            'was_reset' => false,
            'was_reconfigured' => false,
        ];
    }

    /**
     * Delete an index.
     *
     * @param IndexUUID $indexUUID
     */
    public function deleteIndex(IndexUUID $indexUUID)
    {
        if (!array_key_exists($this->getIndexKey($indexUUID), $this->indices)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        unset($this->indices[$this->getIndexKey($indexUUID)]);
    }

    /**
     * Reset the index.
     *
     * @param IndexUUID $indexUUID
     */
    public function resetIndex(IndexUUID $indexUUID)
    {
        if (!array_key_exists($this->getIndexKey($indexUUID), $this->indices)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $this->indices[$this->getIndexKey($indexUUID)]['was_reset'] = true;
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
        return array_key_exists($this->getIndexKey($indexUUID), $this->indices);
    }

    /**
     * Config the index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        IndexUUID $indexUUID,
        Config $config
    ) {
        if (!array_key_exists($this->getIndexKey($indexUUID), $this->indices)) {
            throw ResourceNotAvailableException::indexNotAvailable('Index not available in InMemoryRepository');
        }

        $this->indices[$this->getIndexKey($indexUUID)]['was_reconfigured'] = true;
    }

    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $this->tokens[$this->getAppKey()][$token->getTokenUUID()->composeUUID()] = $token;
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        unset($this->tokens[$this->getAppKey()][$tokenUUID->composeUUID()]);
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens[$this->getAppKey()] ?? [];
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        $this->tokens[$this->getAppKey()] = [];
    }

    /**
     * Get index position by credentials.
     *
     * @param IndexUUID $indexUUID
     *
     * @return string
     */
    private function getIndexKey(IndexUUID $indexUUID): string
    {
        return $this->getAppKey().'_'.$indexUUID->getId();
    }

    /**
     * Get app position by credentials.
     *
     * @return string
     */
    private function getAppKey(): string
    {
        $appUUID = $this
            ->getRepositoryReference()
            ->getAppUUID();

        return $appUUID instanceof AppUUID
            ? $appUUID->getId()
            : '';
    }
}
