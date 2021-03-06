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

namespace Apisearch\Tests\Exporter;

use Apisearch\Exception\ExporterFormatNotImplementedException;
use Apisearch\Exporter\CSVExporter;
use Apisearch\Exporter\ExporterCollection;
use Apisearch\Exporter\JSONExporter;
use PHPUnit\Framework\TestCase;

/**
 * Class ExporterCollectionTest.
 */
class ExporterCollectionTest extends TestCase
{
    /**
     * Test collection.
     */
    public function testCollection()
    {
        $exporterCollection = new ExporterCollection();
        $jsonExporter = new JSONExporter();
        $exporterCollection->addExporter($jsonExporter);
        $csvExporter = new CSVExporter();
        $exporterCollection->addExporter($csvExporter);
        $this->assertSame($jsonExporter, $exporterCollection->getExporterByName('json'));
        $this->assertSame($csvExporter, $exporterCollection->getExporterByName('csv'));
    }

    /**
     * Test collection with exception.
     */
    public function testCollectionException()
    {
        $exporterCollection = new ExporterCollection();
        $exporterCollection->addExporter(new JSONExporter());
        $exporterCollection->addExporter(new CSVExporter());
        $this->expectException(ExporterFormatNotImplementedException::class);
        $exporterCollection->getExporterByName('xml');
    }
}
