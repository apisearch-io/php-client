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

namespace Apisearch\Tests\Exporter;

use Apisearch\Exporter\Exporter;
use Apisearch\Model\Coordinate;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;

/**
 * Class ExporterTest.
 */
abstract class ExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get exporter instance.
     *
     * @return Exporter
     */
    abstract public function getExporterInstance(): Exporter;

    /**
     * Test import + export is exactly the same format.
     */
    public function testImportExportIsTheSame()
    {
        $items = [
            Item::create(
                ItemUUID::createByComposedUUID('123~abc'),
                [
                    'ean' => '7d8s7d89s7d89s',
                    'name' => 'product name',
                    'description' => 'product description',
                    'punctuation' => 2.4,
                    'now' => (new \DateTime())->format(DATE_ATOM),
                ],
                [
                    'category_id' => 4,
                    'manufacturer_id' => '78ds7ds9',
                    'tags' => [
                        'tag1',
                        'tag2',
                        'tag3',
                    ],
                ],
                [
                    'name' => 'product name',
                    'description' => 'product description',
                ],
                [
                    '7d8s7d89s7d89s',
                ],
                [
                    'suggest-1',
                    'suggest-2',
                ]
            ),
            Item::createLocated(
                ItemUUID::createByComposedUUID('567~xyz'),
                Coordinate::createFromArray([
                    'lon' => 43.22,
                    'lat' => 43.33,
                ]),
                [
                    'ean' => '7d8s7d84444s',
                    'name' => 'product name',
                ],
                [
                    'category_id' => 4,
                ],
                [
                    'name' => 'product name',
                ],
                [
                    '7d8s7d89s7d89s',
                ],
                [
                    'suggest-1',
                ]
            ),
        ];

        $this->assertEquals(
            $items,
            $this
                ->getExporterInstance()
                ->formatToItems(
                    $this
                        ->getExporterInstance()
                        ->itemsToFormat($items)
                )
        );
    }
}
