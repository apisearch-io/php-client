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

namespace Apisearch\Transformer;

use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Transformer.
 */
class Transformer
{
    /**
     * @var EventDispatcherInterface
     *
     * Event dispatcher
     */
    private $eventDispatcher;

    /**
     * @var ReadTransformer[]
     *
     * Read transformers
     */
    private $readTransformers = [];

    /**
     * @var WriteTransformer[]
     *
     * Write transformers
     */
    private $writeTransformers = [];

    /**
     * Transformer constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Add an read transformer.
     *
     * @param ReadTransformer $readTransformer
     */
    public function addReadTransformer(ReadTransformer $readTransformer)
    {
        $this->readTransformers[] = $readTransformer;
    }

    /**
     * Add an write transformer.
     *
     * @param WriteTransformer $writeTransformer
     */
    public function addWriteTransformer(WriteTransformer $writeTransformer)
    {
        $this->writeTransformers[] = $writeTransformer;
    }

    /**
     * Transform a set of items into a set of objects.
     *
     * @param Item[] $items
     *
     * @return array
     */
    public function fromItems(array $items): array
    {
        $objects = [];
        foreach ($items as $item) {
            $objects[] = $this->fromItem($item);
        }

        return $objects;
    }

    /**
     * Transform an item into an object.
     *
     * @param Item $item
     *
     * @return mixed
     */
    public function fromItem(Item $item)
    {
        foreach ($this->readTransformers as $readTransformer) {
            if ($readTransformer->isValidItem($item)) {
                return $readTransformer->fromItem($item);
            }
        }

        return $item;
    }

    /**
     * Transform a set of objects into a set of items.
     *
     * @param array $objects
     *
     * @return Item[]
     */
    public function toItems(array $objects): array
    {
        $items = [];
        foreach ($objects as $object) {
            $item = $this->toItem($object);
            if ($item instanceof Item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Transform an object into an item.
     *
     * @param mixed $object
     *
     * @return Item|null
     */
    public function toItem($object): ? Item
    {
        foreach ($this->writeTransformers as $writeTransformer) {
            if ($writeTransformer->isValidObject($object)) {
                $item = $writeTransformer->toItem($object);
                $this
                    ->eventDispatcher
                    ->dispatch(
                        'puntmig_search.item_transformed',
                        new ItemTransformed(
                            $item,
                            $object
                        )
                    );

                return $item;
            }
        }

        return null;
    }

    /**
     * Transform a set of objects into a set of itemUUIDs.
     *
     * @param array $objects
     *
     * @return ItemUUID[]
     */
    public function toItemUUIDs(array $objects): array
    {
        $itemUUIDs = [];
        foreach ($objects as $object) {
            $itemUUID = $this->toItemUUID($object);
            if ($itemUUID instanceof ItemUUID) {
                $itemUUIDs[] = $itemUUID;
            }
        }

        return $itemUUIDs;
    }

    /**
     * Transform an object into an itemUUID or return NULL if the object is not
     * transformable.
     *
     * @param mixed $object
     *
     * @return ItemUUID|null
     */
    public function toItemUUID($object): ? ItemUUID
    {
        foreach ($this->writeTransformers as $writeTransformer) {
            if ($writeTransformer->isValidObject($object)) {
                return $writeTransformer->toItemUUID($object);
            }
        }

        return null;
    }
}
