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

use Apisearch\Exception\ExporterFormatNotImplementedException;

/**
 * ExporterCollection.
 */
class ExporterCollection
{
    /**
     * @var Exporter[]
     *
     * Exporters
     */
    private $exporters = [];

    /**
     * Add exporter.
     *
     * @param Exporter $exporter
     */
    public function addExporter(Exporter $exporter)
    {
        $this->exporters[] = $exporter;
    }

    /**
     * Get exporter by name.
     *
     * @param string $exporterName
     *
     * @return Exporter
     *
     * @throws ExporterFormatNotImplementedException
     */
    public function getExporterByName(string $exporterName)
    {
        foreach ($this->exporters as $exporter) {
            if ($exporterName === $exporter->getName()) {
                return $exporter;
            }
        }

        throw new ExporterFormatNotImplementedException();
    }
}
