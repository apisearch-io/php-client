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

namespace Apisearch\Repository;

use Apisearch\Model\TokenUUID;

/**
 * Class RepositoryWithCredentials.
 */
abstract class RepositoryWithCredentials implements WithRepositoryReference
{
    use WithRepositoryReferenceTrait;

    /**
     * @var TokenUUID
     *
     * Token UUID
     */
    private $tokenUUID;

    /**
     * Set credentials.
     *
     * @param RepositoryReference $repositoryReference
     * @param TokenUUID           $tokenUUID
     */
    public function setCredentials(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    ) {
        $this->tokenUUID = $tokenUUID;
        $this->setRepositoryReference($repositoryReference);
    }

    /**
     * Get tokenUUID.
     *
     * @return TokenUUID|null
     */
    public function getTokenUUID(): ? TokenUUID
    {
        return $this->tokenUUID;
    }
}
