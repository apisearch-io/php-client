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
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ItemTransformed.
 */
class ItemTransformed extends Event
{
    /**
     * @var Item
     *
     * Item
     */
    private $item;

    /**
     * @var mixed
     *
     * Original object
     */
    private $originalObject;

    /**
     * ItemTransformed constructor.
     *
     * @param Item  $item
     * @param mixed $originalObject
     */
    public function __construct(
        Item $item,
        $originalObject
    ) {
        $this->item = $item;
        $this->originalObject = $originalObject;
    }

    /**
     * Get Item.
     *
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Get OriginalObject.
     *
     * @return mixed
     */
    public function getOriginalObject()
    {
        return $this->originalObject;
    }
}
