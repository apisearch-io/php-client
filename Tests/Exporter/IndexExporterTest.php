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

use Apisearch\Config\ImmutableConfig;
use Apisearch\Exporter\ExporterCollection;
use Apisearch\Exporter\IndexExporter;
use Apisearch\Exporter\JSONExporter;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\InMemoryRepository;
use Apisearch\Repository\RepositoryReference;

/**
 * Class IndexExporterTest.
 */
class IndexExporterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test import and export.
     */
    public function testImportAndExport()
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create('xxx', 'xxx'));
        $repository->createIndex(ImmutableConfig::createEmpty());
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~1')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~2')));
        $repository->addItem(Item::create(ItemUUID::createByComposedUUID('product~3')));
        $repository->flush();
        $this->assertCount(3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );

        $exporterCollection = new ExporterCollection();
        $exporterCollection->addExporter(new JSONExporter());
        $indexExporter = new IndexExporter($exporterCollection);
        $data = $indexExporter->exportIndex($repository, 'json');
        $repository->resetIndex();
        $this->assertCount(0,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $indexExporter->importIndex($repository, $data, 'json');
        $this->assertCount(3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );
        $this->assertEquals(
            $data,
            $indexExporter->exportIndex($repository, 'json')
        );
    }
}
