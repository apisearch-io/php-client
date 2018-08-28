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

use Apisearch\Exporter\ExporterCollection;
use Apisearch\Exporter\IndexExporter;
use Apisearch\Exporter\JSONExporter;
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\Item;
use Apisearch\Model\ItemUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\InMemoryRepository;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;

/**
 * Class IndexExporterTest.
 */
class IndexExporterTest extends TestCase
{
    /**
     * Test import and export.
     */
    public function testImportAndExport()
    {
        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
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
        $this->assertCount(3,
            $repository
                ->query(Query::createMatchAll())
                ->getItems()
        );

        $repository = new InMemoryRepository();
        $repository->setRepositoryReference(RepositoryReference::create(AppUUID::createById('xxx'), IndexUUID::createById('xxx')));
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
