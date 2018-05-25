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

use Apisearch\Model\Changes;
use PHPUnit_Framework_TestCase;

/**
 * Class ChangesTest.
 */
class ChangesTest extends PHPUnit_Framework_TestCase
{
    /**
     * test empty creation.
     */
    public function testEmptyCreation()
    {
        $changes = Changes::create();
        $this->assertEmpty($changes->getChanges());
    }

    /**
     * Test add change.
     */
    public function testAddChange()
    {
        $changes = Changes::create();
        $changes->addChange(
            'field1',
            'value1',
            Changes::TYPE_VALUE
        );
        $changesValues = $changes->getChanges();
        $firstChange = reset($changesValues);
        $this->assertEquals('field1', $firstChange['field']);
        $this->assertEquals('value1', $firstChange['value']);
        $this->assertEquals(Changes::TYPE_VALUE, $firstChange['type']);
    }

    /**
     * Test multiple add changes.
     */
    public function testMultipleAddChange()
    {
        $changes = Changes::create();
        $changes
            ->addChange(
                'field1',
                'value1',
                Changes::TYPE_VALUE
            )
            ->addChange(
                'field2',
                'value2',
                Changes::TYPE_LITERAL
            );
        $changesValues = $changes->getChanges();
        $firstChange = reset($changesValues);
        $this->assertEquals('field1', $firstChange['field']);
        $this->assertEquals('value1', $firstChange['value']);
        $this->assertEquals(Changes::TYPE_VALUE, $firstChange['type']);
        $nextChange = next($changesValues);
        $this->assertEquals('field2', $nextChange['field']);
        $this->assertEquals('value2', $nextChange['value']);
        $this->assertEquals(Changes::TYPE_LITERAL, $nextChange['type']);
    }

    /**
     * Test array changes.
     */
    public function testArrayChanges()
    {
        $changes = Changes::create();
        $changes
            ->addElementInList(
                'field1',
                'value1',
                Changes::TYPE_VALUE
            )
            ->deleteElementFromList(
                'field2',
                'condition2'
            )
            ->updateElementFromList(
                'field3',
                'condition3',
                'value3',
                Changes::TYPE_LITERAL
            );
        $changesValues = $changes->getChanges();

        $firstChange = reset($changesValues);
        $this->assertEquals('field1', $firstChange['field']);
        $this->assertEquals('value1', $firstChange['value']);
        $this->assertFalse(array_key_exists('condition', $firstChange));
        $this->assertGreaterThan(0, $firstChange['type'] & Changes::TYPE_ARRAY_ELEMENT_ADD);
        $this->assertGreaterThan(0, $firstChange['type'] & Changes::TYPE_ARRAY);
        $this->assertGreaterThan(0, $firstChange['type'] & Changes::TYPE_VALUE);

        $secondChange = next($changesValues);
        $this->assertEquals('field2', $secondChange['field']);
        $this->assertFalse(array_key_exists('value', $secondChange));
        $this->assertEquals('condition2', $secondChange['condition']);
        $this->assertGreaterThan(0, $secondChange['type'] & Changes::TYPE_ARRAY_ELEMENT_DELETE);
        $this->assertGreaterThan(0, $secondChange['type'] & Changes::TYPE_ARRAY);

        $thirdChange = next($changesValues);
        $this->assertEquals('field3', $thirdChange['field']);
        $this->assertEquals('value3', $thirdChange['value']);
        $this->assertEquals('condition3', $thirdChange['condition']);
        $this->assertGreaterThan(0, $thirdChange['type'] & Changes::TYPE_ARRAY_ELEMENT_UPDATE);
        $this->assertGreaterThan(0, $thirdChange['type'] & Changes::TYPE_ARRAY);
        $this->assertGreaterThan(0, $thirdChange['type'] & Changes::TYPE_LITERAL);
    }

    /**
     * Test to array.
     */
    public function testToArray()
    {
        $changes = Changes::create();
        $changes
            ->addElementInList(
                'field1',
                'value1',
                Changes::TYPE_VALUE
            )
            ->deleteElementFromList(
                'field2',
                'condition2'
            )
            ->updateElementFromList(
                'field3',
                'condition3',
                'value3',
                Changes::TYPE_LITERAL
            );

        $this->assertEquals([
            [
                'field' => 'field1',
                'value' => 'value1',
                'type' => (Changes::TYPE_VALUE | Changes::TYPE_ARRAY_ELEMENT_ADD),
            ],
            [
                'field' => 'field2',
                'condition' => 'condition2',
                'type' => Changes::TYPE_ARRAY_ELEMENT_DELETE,
            ],
            [
                'field' => 'field3',
                'condition' => 'condition3',
                'value' => 'value3',
                'type' => Changes::TYPE_LITERAL | Changes::TYPE_ARRAY_ELEMENT_UPDATE,
            ],
        ], $changes->toArray());
    }

    /**
     * Test http transport.
     */
    public function testHttpTransport()
    {
        $changes = Changes::create();
        $changes
            ->addElementInList(
                'field1',
                'value1',
                Changes::TYPE_VALUE
            )
            ->deleteElementFromList(
                'field2',
                'condition2'
            )
            ->updateElementFromList(
                'field3',
                'condition3',
                'value3',
                Changes::TYPE_LITERAL
            );

        $changesArray = $changes->toArray();
        $this->assertEquals(
            $changesArray,
            Changes::createFromArray($changesArray)->toArray()
        );
    }
}
