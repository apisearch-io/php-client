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

use Apisearch\Model\Coordinate;
use Apisearch\Model\Item;

/**
 * CSVExporter.
 */
class CSVExporter implements Exporter
{
    /**
     * Get parser name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'csv';
    }

    /**
     * Convert array of Items to string format.
     *
     * @param Item[] $items
     *
     * @return string
     */
    public function itemsToFormat(array $items): string
    {
        return $this->strToCSV(array_map(function (Item $item) {
            return [
                $item->getId(),
                $item->getType(),
                json_encode($item->getMetadata()),
                json_encode($item->getIndexedMetadata()),
                json_encode($item->getSearchableMetadata()),
                json_encode($item->getExactMatchingMetadata()),
                json_encode($item->getSuggest()),
                json_encode(
                    ($item->getCoordinate() instanceof Coordinate)
                        ? $item->getCoordinate()->toArray()
                        : null
                ),
            ];
        }, $items));
    }

    /**
     * Convert string formatted to array of Items.
     *
     * @param string $data
     *
     * @return Item[]
     */
    public function formatToItems(string $data): array
    {
        return array_map(function (string $line) {
            $item = str_getcsv($line, ',');
            $itemAsArray = [
                'uuid' => [
                    'id' => $item[0],
                    'type' => $item[1],
                ],
                'metadata' => json_decode($item[2], true),
                'indexed_metadata' => json_decode($item[3], true),
                'searchable_metadata' => json_decode($item[4], true),
                'exact_matching_metadata' => json_decode($item[5], true),
                'suggest' => json_decode($item[6], true),
            ];

            if (!empty($item[7])) {
                $itemAsArray['coordinate'] = json_decode($item[7], true);
            }

            return Item::createFromArray($itemAsArray);
        }, str_getcsv($data, "\n"));
    }

    /**
     * @param array  $items
     * @param string $delimiter
     * @param string $enclosure
     *
     * @return string
     */
    private function strToCSV(
        array $items,
        string $delimiter = ',',
        string $enclosure = '"'
    ) {
        $fp = fopen('php://temp', 'r+b');
        foreach ($items as $item) {
            fputcsv($fp, $item, $delimiter, $enclosure);
        }
        rewind($fp);
        $data = rtrim(stream_get_contents($fp), "\n");
        fclose($fp);

        return $data;
    }
}
