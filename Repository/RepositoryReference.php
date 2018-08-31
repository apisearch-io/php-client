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
 * Class RepositoryReference.
 */
class RepositoryReference
{
    /**
     * @var AppUUID|null
     *
     * App uuid
     */
    protected $appUUID;

    /**
     * @var IndexUUID|null
     *
     * Index uuid
     */
    protected $indexUUID;

    /**
     * RepositoryReference constructor.
     *
     * @param AppUUID   $appUUID
     * @param IndexUUID $indexUUID
     */
    private function __construct(
        AppUUID $appUUID = null,
        IndexUUID $indexUUID = null
    ) {
        $this->appUUID = $appUUID;
        $this->indexUUID = $indexUUID;
    }

    /**
     * Get AppUUID.
     *
     * @return AppUUID|null
     */
    public function getAppUUID(): ? AppUUID
    {
        return $this->appUUID;
    }

    /**
     * Get IndexUUID.
     *
     * @return IndexUUID|null
     */
    public function getIndexUUID(): ? IndexUUID
    {
        return $this->indexUUID;
    }

    /**
     * Create by appUUID and indexUUID.
     *
     * @param AppUUID   $appUUID
     * @param IndexUUID $indexUUID
     *
     * @return RepositoryReference
     */
    public static function create(
        AppUUID $appUUID = null,
        IndexUUID $indexUUID = null
    ): RepositoryReference {
        return new self($appUUID, $indexUUID);
    }

    /**
     * Change the index.
     *
     * @param IndexUUID $indexUUID
     *
     * @return RepositoryReference
     */
    public function changeIndex(IndexUUID $indexUUID): RepositoryReference
    {
        return self::create(
            $this->appUUID,
            $indexUUID
        );
    }

    /**
     * Compose.
     *
     * @return string
     */
    public function compose(): string
    {
        return sprintf('%s_%s',
            $this->appUUID instanceof AppUUID
                ? str_replace('_', '-', $this->appUUID->composeUUID())
                : '',
            $this->indexUUID instanceof IndexUUID
                ? str_replace('_', '-', $this->indexUUID->composeUUID())
                : ''
        );
    }

    /**
     * Create from composed.
     *
     * @param string $composed
     *
     * @return RepositoryReference
     */
    public static function createFromComposed(string $composed): RepositoryReference
    {
        list($appUUIDComposed, $indexUUIDComposed) = explode('_', $composed, 2);

        return RepositoryReference::create(
            AppUUID::createById($appUUIDComposed),
            IndexUUID::createById($indexUUIDComposed)
        );
    }
}
