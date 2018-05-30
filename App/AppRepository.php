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
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\App;

use Apisearch\Repository\WithRepositoryReference;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

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
}
