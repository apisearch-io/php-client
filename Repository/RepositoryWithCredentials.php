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

namespace Apisearch\Repository;

/**
 * Class RepositoryWithCredentials.
 */
abstract class RepositoryWithCredentials implements WithRepositoryReference
{
    use WithRepositoryReferenceTrait;

    /**
     * @var string
     *
     * Api Token
     */
    private $token;

    /**
     * Set credentials.
     *
     * @param RepositoryReference $repositoryReference
     * @param string              $token
     */
    public function setCredentials(
        RepositoryReference $repositoryReference,
        string $token
    ) {
        $this->token = $token;
        $this->setRepositoryReference($repositoryReference);
    }

    /**
     * Get token.
     *
     * @return string|null
     */
    public function getToken(): ? string
    {
        return $this->token;
    }
}
