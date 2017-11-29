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
