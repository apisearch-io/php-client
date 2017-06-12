<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Transformer;

use Puntmig\Search\Exception\TransformerException;
use Puntmig\Search\Model\Item;

/**
 * Class Transformer.
 */
class Transformer
{
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
    public function fromItems(array $items) : array
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
     *
     * @throws TransformerException Unable to create Item
     */
    public function toItems(array $objects) : array
    {
        $items = [];
        foreach ($objects as $object) {
            $items[] = $this->toItem($object);
        }

        return $items;
    }

    /**
     * Transform an object into an item.
     *
     * @param mixed $object
     *
     * @return Item
     *
     * @throws TransformerException Unable to create Item
     */
    public function toItem($object) : Item
    {
        foreach ($this->writeTransformers as $writeTransformer) {
            if ($writeTransformer->isValidObject($object)) {
                return $writeTransformer->toItem($object);
            }
        }

        throw TransformerException::createUnableToCreateItemException($object);
    }
}
