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
use Apisearch\Model\Index;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Token;
use Apisearch\Model\TokenUUID;
use Apisearch\Repository\WithRepositoryReference;

/**
 * Class AppRepository.
 */
interface AppRepository extends WithRepositoryReference
{
    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token);

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID);

    /**
     * Get tokens.
     *
     * @return Token[]
     */
    public function getTokens(): array;

    /**
     * Purge tokens.
     */
    public function deleteTokens();

    /**
     * Get indices.
     *
     * @return Index[]
     */
    public function getIndices(): array;

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
    );

    /**
     * Delete an index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex(IndexUUID $indexUUID);

    /**
     * Reset the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex(IndexUUID $indexUUID);

    /**
     * Checks the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @return bool
     */
    public function checkIndex(IndexUUID $indexUUID): bool;

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
    );
}
