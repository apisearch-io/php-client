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

namespace Apisearch\Tests\Model;

use Apisearch\Model\ItemUUID;
use PHPUnit_Framework_TestCase;

/**
 * Class ItemUUIDTest.
 */
class ItemUUIDTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create from array.
     */
    public function testCreateFromArray()
    {
        $uuidArray = [
            'id' => '1',
            'type' => 'product',
        ];

        $uuid = ItemUUID::createFromArray($uuidArray);
        $this->assertSame('1', $uuid->getId());
        $this->assertSame('product', $uuid->getType());
    }

    /**
     * Test create.
     */
    public function testCreate()
    {
        $uuid = new ItemUUID('1', 'product');
        $this->assertSame('1', $uuid->getId());
        $this->assertSame('product', $uuid->getType());
    }

    /**
     * Test compose UUID.
     */
    public function testComposeUUID()
    {
        $uuid = new ItemUUID('1', 'product');
        $this->assertEquals('1~product', $uuid->composeUUID());
    }

    /**
     * Test create by composed UUID.
     */
    public function testCreateByComposedUUID()
    {
        $itemUUID = ItemUUID::createByComposedUUID('1~type');
        $this->assertSame('type', $itemUUID->getType());
        $this->assertSame('1', $itemUUID->getId());
    }

    /**
     * Test create by composed UUID with exception.
     *
     * @dataProvider dataCreateByComposedUUIDException
     *
     * @expectedException \Apisearch\Exception\UUIDException
     */
    public function testCreateByComposedUUIDException(string $composedUUID)
    {
        ItemUUID::createByComposedUUID($composedUUID);
    }

    /**
     * Data for testCreateByComposedUUIDException.
     */
    public function dataCreateByComposedUUIDException()
    {
        return [
            ['item'],
            [''],
            ['1'],
        ];
    }
}
