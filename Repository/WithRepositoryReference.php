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
 * Class WithRepositoryReference.
 */
interface WithRepositoryReference
{
    /**
     * Set repository reference.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function setRepositoryReference(RepositoryReference $repositoryReference);

    /**
     * Get RepositoryReference.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference;
}
