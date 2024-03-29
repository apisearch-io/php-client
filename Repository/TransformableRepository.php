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

use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Model\Changes;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\TokenUUID;
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
     * Set credentials.
     *
     * @param RepositoryReference $repositoryReference
     * @param TokenUUID           $tokenUUID
     */
    public function setCredentials(
        RepositoryReference $repositoryReference,
        TokenUUID $tokenUUID
    ) {
        parent::setCredentials($repositoryReference, $tokenUUID);
        $this
            ->repository
            ->setCredentials($repositoryReference, $tokenUUID);
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
     * @param array $parameters
     *
     * @return Result
     *
     * @throws ResourceNotAvailableException
     */
    public function query(
        Query $query,
        array $parameters = []
    ): Result {
        $result = $this
            ->repository
            ->query($query, $parameters);

        return $this->applyTransformersToResult($result);
    }

    /**
     * Apply transformers on Result.
     *
     * @param Result $result
     *
     * @return Result
     */
    private function applyTransformersToResult(Result $result): Result
    {
        return empty($result->getSubresults())
            ? Result::create(
                $result->getQuery(),
                $result->getTotalItems(),
                $result->getTotalHits(),
                $result->getAggregations(),
                $result->getSuggestions(),
                $this
                    ->transformer
                    ->fromItems(
                        $result->getItems()
                    )
            )
            : Result::createMultiResult(
                array_map(function (Result $subresult) {
                    return $this->applyTransformersToResult($subresult);
                }, $result->getSubresults())
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
     * Delete items by query.
     *
     * @param Query $query
     */
    public function deleteItemsByQuery(Query $query)
    {
        $this
            ->repository
            ->deleteItemsByQuery($query);
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
