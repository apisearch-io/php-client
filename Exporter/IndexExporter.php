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

namespace Apisearch\Exporter;

use Apisearch\Query\Query;
use Apisearch\Repository\Repository;

/**
 * Class IndexExporter.
 */
class IndexExporter
{
    /**
     * @var ExporterCollection
     *
     * Exporters
     */
    private $exporterCollection;

    /**
     * IndexExporter constructor.
     *
     * @param ExporterCollection $exporterCollection
     */
    public function __construct(ExporterCollection $exporterCollection)
    {
        $this->exporterCollection = $exporterCollection;
    }

    /**
     * Import index.
     *
     * @param Repository $repository
     * @param string     $data
     * @param string     $format
     */
    public function importIndex(
        Repository $repository,
        string $data,
        string $format
    ) {
        $items = $this
            ->exporterCollection
            ->getExporterByName($format)
            ->formatToItems($data);

        $repository->addItems($items);
        $repository->flush();
    }

    /**
     * Export index.
     *
     * @param Repository $repository
     * @param string     $format
     *
     * @return string
     */
    public function exportIndex(
        Repository $repository,
        string $format
    ): string {
        $allItems = [];
        $iteration = 0;
        while (true) {
            $items = $repository
                ->query(Query::create('', $iteration, 10000))
                ->getItems();

            if (empty($items)) {
                break;
            }

            $allItems = array_merge(
                $allItems,
                $items
            );

            ++$iteration;
        }

        return $this
            ->exporterCollection
            ->getExporterByName($format)
            ->itemsToFormat($allItems);
    }
}
