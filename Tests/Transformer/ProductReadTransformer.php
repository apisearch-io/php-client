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

namespace Apisearch\Tests\Transformer;

use Apisearch\Model\Item;
use Apisearch\Transformer\ReadTransformer;
use DateTime;

/**
 * Class ProductReadTransformer.
 */
class ProductReadTransformer implements ReadTransformer
{
    /**
     * The item should be converted by this transformer.
     *
     * @param Item $item
     *
     * @return bool
     */
    public function isValidItem(Item $item): bool
    {
        return 'product' === $item
            ->getUUID()->getType();
    }

    /**
     * Create object by item.
     *
     * @param Item $item
     *
     * @return mixed
     */
    public function fromItem(Item $item)
    {
        return new Product(
            $item->getId(),
            (string) $item->get('name'),
            DateTime::createFromFormat(DATE_ATOM, $item->get('created_at'))
        );
    }
}
