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

use Apisearch\Model\Metadata;
use PHPUnit\Framework\TestCase;

/**
 * Class MetadataTest.
 */
class MetadataTest extends TestCase
{
    /**
     * Test to metadata.
     */
    public function testToMetadata()
    {
        $this->assertEquals(
            'id##1~~name##product~~level##5',
            Metadata::toMetadata([
                'id' => 1,
                'name' => 'product',
                'level' => '5',
            ])
        );
    }

    /**
     * Test from metadata.
     */
    public function testFromMetadata()
    {
        $this->assertEquals(
            [
                'id' => '1',
                'name' => 'product',
                'level' => '5',
            ],
            Metadata::fromMetadata('id##1~~name##product~~level##5')
        );

        $this->assertEquals(
            [
                'id' => 'my-id',
                'name' => 'my-id',
            ],
            Metadata::fromMetadata('my-id')
        );

        $this->assertNull(
            Metadata::fromMetadata('name##product~~level##1')
        );
    }
}
