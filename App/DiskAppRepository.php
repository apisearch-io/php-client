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
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;

/**
 * Class DiskAppRepository.
 */
class DiskAppRepository extends InMemoryAppRepository
{
    /**
     * @var string
     *
     * File name
     */
    private $filename;

    /**
     * DiskAppRepository constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Get indices.
     *
     * @return Index[]
     */
    public function getIndices(): array
    {
        $this->load();

        return parent::getIndices();
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
        $this->load();
        parent::createIndex(
            $indexUUID,
            $config
        );
        $this->save();
    }

    /**
     * Delete an index.
     *
     * @param IndexUUID $indexUUID
     */
    public function deleteIndex(IndexUUID $indexUUID)
    {
        $this->load();
        parent::deleteIndex($indexUUID);
        $this->save();
    }

    /**
     * Reset the index.
     *
     * @param IndexUUID $indexUUID
     */
    public function resetIndex(IndexUUID $indexUUID)
    {
        $this->load();
        parent::resetIndex($indexUUID);
        $this->save();
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
        $this->load();

        return parent::checkIndex($indexUUID);
    }

    /**
     * Config the index.
     *
     * @param IndexUUID $indexUUID
     * @param Config    $config
     * @param bool $forceReindex
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(
        IndexUUID $indexUUID,
        Config $config,
        bool $forceReindex = false
    ) {
        $this->load();
        parent::configureIndex(
            $indexUUID,
            $config,
            $forceReindex
        );
        $this->save();
    }

    /**
     * Add token.
     *
     * @param Token $token
     */
    public function putToken(Token $token)
    {
        $this->load();
        parent::putToken($token);
        $this->save();
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        $this->load();
        parent::deleteToken($tokenUUID);
        $this->save();
    }

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array
    {
        $this->load();

        return parent::getTokens();
    }

    /**
     * Delete all tokens.
     */
    public function deleteTokens()
    {
        $this->load();
        parent::deleteTokens();
        $this->save();
    }

    /**
     * Load.
     */
    private function load()
    {
        if (!file_exists($this->filename)) {
            return;
        }

        list($tokens, $indices) = unserialize(
            file_get_contents(
                $this->filename
            )
        );

        $this->tokens = $tokens ?? [];
        $this->indices = $indices ?? [];
    }

    /**
     * Save.
     */
    private function save()
    {
        file_put_contents(
            $this->filename,
            serialize([
                $this->tokens,
                $this->indices,
            ])
        );
        $this->tokens = [];
        $this->indices = [];
    }
}
