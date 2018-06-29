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

namespace Apisearch\User;

use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class InMemoryUserRepository.
 */
class InMemoryUserRepository extends RepositoryWithCredentials implements UserRepository
{
    /**
     * Add interaction.
     *
     * @param Interaction $interaction
     */
    public function addInteraction(Interaction $interaction)
    {
        // TODO: Implement addInteraction() method.
    }

    /**
     * Delete all interactions.
     */
    public function deleteAllInteractions()
    {
        // TODO: Implement deleteAllInteractions() method.
    }
}
