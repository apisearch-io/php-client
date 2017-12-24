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

use Apisearch\Exporter\CSVExporter;
use Apisearch\Exporter\ExporterCollection;
use Apisearch\Exporter\JSONExporter;

/**
 * Class ExporterCollectionTest.
 */
class ExporterCollectionTest extends \PHPUnit_Framework_TestCase
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
     *
     * @expectedException \Apisearch\Exception\ExporterFormatNotImplementedException
     */
    public function testCollectionException()
    {
        $exporterCollection = new ExporterCollection();
        $exporterCollection->addExporter(new JSONExporter());
        $exporterCollection->addExporter(new CSVExporter());
        $exporterCollection->getExporterByName('xml');
    }
}
