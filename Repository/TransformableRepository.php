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
        $this->repositoryReference = $repositoryReference;
        $this
            ->repository
            ->setRepositoryReference($repositoryReference);
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
     * Reset the index.
     *
     * @var null|string
     */
    public function reset(? string $language)
    {
        $this
            ->repository
            ->reset($language);
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
