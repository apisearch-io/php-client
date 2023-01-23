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

namespace Apisearch\Tests\Model;

use Apisearch\Model\ItemForDeletion;
use Apisearch\Model\ItemUUID;
use PHPUnit\Framework\TestCase;

class ItemForDeletionTest extends TestCase
{
    public function testCreation()
    {
        $item = ItemForDeletion::createByUUID(ItemUUID::createByComposedUUID('1~product'));
        $this->assertEquals('1~product', $item->composeUUID());
        $this->assertInstanceOf(ItemForDeletion::class, $item);
        $item = ItemForDeletion::createFromArray($item->toArray());
        $this->assertEquals('1~product', $item->composeUUID());
        $this->assertInstanceOf(ItemForDeletion::class, $item);
    }
}
