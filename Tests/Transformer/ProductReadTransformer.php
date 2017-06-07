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

namespace Puntmig\Search\Tests\Transformer;

use DateTime;

use Puntmig\Search\Model\Item;
use Puntmig\Search\Transformer\ReadTransformer;

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
    public function isValidItem(Item $item) : bool
    {
        return $item
            ->getUUID()->getType() === 'product';
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
