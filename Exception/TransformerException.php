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

namespace Puntmig\Search\Exception;

use LogicException;

/**
 * Class TransformerException.
 */
class TransformerException extends LogicException
{
    /**
     * Return new unable to create an item by an object exception.
     *
     * @param mixed $object
     *
     * @return TransformerException
     */
    public static function createUnableToCreateItemException($object) : TransformerException
    {
        return new self(sprintf('Unable to create a new Item instance given type %s. Check that the transformer is properly initialized',
            get_class($object)
        ));
    }
}
