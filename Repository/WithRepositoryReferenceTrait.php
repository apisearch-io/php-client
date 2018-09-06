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

use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;

/**
 * Class WithRepositoryReferenceTrait.
 */
trait WithRepositoryReferenceTrait
{
    /**
     * @var RepositoryReference
     *
     * Repository Reference
     */
    private $repositoryReference;

    /**
     * Set repository reference.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function setRepositoryReference(RepositoryReference $repositoryReference)
    {
        $this->repositoryReference = $repositoryReference;
    }

    /**
     * Get RepositoryReference.
     *
     * @return RepositoryReference
     */
    public function getRepositoryReference(): RepositoryReference
    {
        return $this->repositoryReference;
    }

    /**
     * Get AppUUID.
     *
     * @return AppUUID|null
     */
    public function getAppUUID(): ? AppUUID
    {
        return $this
            ->repositoryReference
            ->getAppUUID();
    }

    /**
     * Get IndexUUID.
     *
     * @return IndexUUID|null
     */
    public function getIndexUUID(): ? IndexUUID
    {
        return $this
            ->repositoryReference
            ->getIndexUUID();
    }
}
