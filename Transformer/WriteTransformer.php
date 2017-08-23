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

use Puntmig\Search\Model\Item;
use Puntmig\Search\Model\ItemUUID;

/**
 * Interface WriteTransformer.
 */
interface WriteTransformer
{
    /**
     * Is an indexable object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function isValidObject($object): bool;

    /**
     * Create item by object.
     *
     * @param mixed $object
     *
     * @return Item
     */
    public function toItem($object): Item;

    /**
     * Create item UUID by object.
     *
     * @param mixed $object
     *
     * @return ItemUUID
     */
    public function toItemUUID($object): ItemUUID;
}
