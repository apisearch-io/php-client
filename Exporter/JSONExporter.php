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
 * JSONExporter.
 */
class JSONExporter implements Exporter
{
    /**
     * Convert array of Items to string format.
     *
     * @param Item[] $items
     *
     * @return string
     */
    public function itemsToFormat(array $items): string
    {
        return json_encode(array_map(function (Item $item) {
            return [
                $item->getId(),
                $item->getType(),
                $item->getMetadata(),
                $item->getIndexedMetadata(),
                $item->getSearchableMetadata(),
                $item->getExactMatchingMetadata(),
                $item->getSuggest(),
                    ($item->getCoordinate() instanceof Coordinate)
                        ? $item->getCoordinate()->toArray()
                        : null,
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
        return array_map(function (array $item) {
            $itemAsArray = [
                'uuid' => [
                    'id' => $item[0],
                    'type' => $item[1],
                ],
                'metadata' => $item[2],
                'indexed_metadata' => $item[3],
                'searchable_metadata' => $item[4],
                'exact_matching_metadata' => $item[5],
                'suggest' => $item[6],
            ];

            if (!empty($item[7])) {
                $itemAsArray['coordinate'] = $item[7];
            }

            return Item::createFromArray($itemAsArray);
        }, json_decode($data, true));
    }
}
