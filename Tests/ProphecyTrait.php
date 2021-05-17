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

namespace Apisearch\Tests;

use Prophecy\PhpUnit\ProphecyTrait as BaseProphecyTrait;

if (trait_exists(BaseProphecyTrait::class)) {
    trait ProphecyTrait
    {
        use BaseProphecyTrait;
    }
} else {
    trait ProphecyTrait
    {
    }
}
