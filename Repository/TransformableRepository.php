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

use Apisearch\Config\Config;
use Apisearch\Config\ImmutableConfig;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Changes;
use Apisearch\Model\Index;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Result\Result;
use Apisearch\Transformer\Transformer;

/**
 * Class TransformableRepository.
 */
class TransformableRepository extends Repository
{
    /**
     * @var Repository
     *
     * Repository decorated
     */
    private $repository;

    /**
     * @var Transformer
     *
     * Item transformer
     */
    private $transformer;

    /**
     * TransformableRepository constructor.
     *
     * @param Repository  $repository
     * @param Transformer $transformer
     */
    public function __construct(
        Repository $repository,
        Transformer $transformer
    ) {
        $this->repository = $repository;
        $this->transformer = $transformer;

        parent::__construct();
    }

    /**
     * Set repository reference.
     *
     * @param RepositoryReference $repositoryReference
     */
    public function setRepositoryReference(RepositoryReference $repositoryReference)
    {
        parent::setRepositoryReference($repositoryReference);
        $this
            ->repository
            ->setRepositoryReference($repositoryReference);
    }

    /**
     * Set repository credentials.
     *
     * @param RepositoryReference $repositoryReference
     * @param string              $token
     */
    public function setCredentials(
        RepositoryReference $repositoryReference,
        string $token
    ) {
        parent::setCredentials($repositoryReference, $token);
        $this
            ->repository
            ->setCredentials($repositoryReference, $token);
    }

    /**
     * Get Repository.
     *
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Flush items.
     *
     * @param Item[]     $itemsToUpdate
     * @param ItemUUID[] $itemsToDelete
     */
    protected function flushItems(
        array $itemsToUpdate,
        array $itemsToDelete
    ) {
        $this
            ->repository
            ->flushItems(
                $itemsToUpdate,
                $itemsToDelete
            );
    }

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    public function query(Query $query): Result
    {
        $result = $this
            ->repository
            ->query($query);

        return Result::create(
            $result->getQuery(),
            $result->getTotalItems(),
            $result->getTotalHits(),
            $result->getAggregations(),
            $result->getSuggests(),
            $this
                ->transformer
                ->fromItems(
                    $result->getItems()
                )
        );
    }

    /**
     * Update items.
     *
     * @param Query   $query
     * @param Changes $changes
     */
    public function updateItems(
        Query $query,
        Changes $changes
    ) {
        $this
            ->repository
            ->updateItems(
                $query,
                $changes
            );
    }

    /**
     * @param string|null $appId
     *
     * @return array|Index[]
     */
    public function getIndices(string $appId = null): array
    {
        $this
            ->repository
            ->getIndices($appId);
    }

    /**
     * Create an index.
     *
     * @param ImmutableConfig $config
     *
     * @throws ResourceExistsException
     */
    public function createIndex(ImmutableConfig $config)
    {
        $this
            ->repository
            ->createIndex($config);
    }

    /**
     * Delete an index.
     *
     * @throws ResourceNotAvailableException
     */
    public function deleteIndex()
    {
        $this
            ->repository
            ->deleteIndex();
    }

    /**
     * Reset the index.
     *
     * @throws ResourceNotAvailableException
     */
    public function resetIndex()
    {
        $this
            ->repository
            ->resetIndex();
    }

    /**
     * Checks the index.
     *
     * @return bool
     */
    public function checkIndex(): bool
    {
        return $this
            ->repository
            ->checkIndex();
    }

    /**
     * Config the index.
     *
     * @param Config $config
     *
     * @throws ResourceNotAvailableException
     */
    public function configureIndex(Config $config)
    {
        $this
            ->repository
            ->configureIndex($config);
    }

    /**
     * Generate item document by a simple object.
     *
     * @param mixed $object
     */
    public function addObject($object)
    {
        $item = $this
            ->transformer
            ->toItem($object);

        if ($item instanceof Item) {
            $this->addItem($item);
        }
    }

    /**
     * Delete item document by uuid.
     *
     * @param mixed $object
     */
    public function deleteObject($object)
    {
        $itemUUID = $this
            ->transformer
            ->toItemUUID($object);

        if ($itemUUID instanceof ItemUUID) {
            $this->deleteItem($itemUUID);
        }
    }
}
