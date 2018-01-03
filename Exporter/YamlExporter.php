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
use Symfony\Component\Yaml\Yaml;

/**
 * YamlExporter.
 */
class YamlExporter implements Exporter
{
    /**
     * Get parser name.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'yml';
    }

    /**
     * Get mime type.
     *
     * @return string
     */
    public static function getMimeType(): string
    {
        return 'application/yaml';
    }

    /**
     * Convert array of Items to string format.
     *
     * This yaml format transforms each item into a plain and logic-less set of
     * data in order to be more comprehensive for the human eyes. To make this
     * happens, this format must work with some headers to define the
     * composition of the item hydration
     *
     * fields:
     *      metadata:
     *          - field1
     *          - field2
     *      indexed_metadata:
     *          - field3
     *          - field4
     *
     * item_1:
     *      ...
     *
     * item_2
     *      ...
     *
     * @param Item[] $items
     *
     * @return string
     */
    public function itemsToFormat(array $items): string
    {
        $headers = [
            'metadata' => [],
            'indexed_metadata' => [],
            'searchable_metadata' => [],
        ];
        $data = [
            'header' => &$headers,
        ];
        $iteration = 0;

        foreach ($items as $item) {
            $this->enrichHeadersWithItem(
                $headers,
                $item
            );

            ++$iteration;
            $data["item_$iteration"] = array_filter(
                (
                    $item->getUUID()->toArray() +
                    $item->getMetadata() +
                    $item->getIndexedMetadata() +
                    $item->getSearchableMetadata() +
                    [
                        'exact_matching' => $item->getExactMatchingMetadata(),
                        'suggest' => $item->getSuggest(),
                        'coordinate' => $item->getCoordinate() instanceof Coordinate
                            ? $item->getCoordinate()->toArray()
                            : null,
                    ]
                ), function ($element) {
                    return
                    !(
                        is_null($element) ||
                        (is_array($element) && empty($element))
                    );
                }
            );
        }

        return Yaml::dump($data);
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
        $data = Yaml::parse($data);
        $header = $data['header'];
        unset($data['header']);
        $items = [];
        foreach ($data as $itemAsArray) {
            $items[] = Item::createFromArray(
                [
                    'uuid' => [
                        'id' => $itemAsArray['id'],
                        'type' => $itemAsArray['type'] ?? 'item',
                    ],
                    'suggest' => ($itemAsArray['suggest'] ?? []),
                    'exact_matching_metadata' => ($itemAsArray['exact_matching'] ?? []),
                    'coordinate' => ($itemAsArray['coordinate'] ?? null),
                ] +
                $this->parseFieldsFromHeaderAndItemAsArray(
                    $header,
                    $itemAsArray
                )
            );
        }

        return $items;
    }

    /**
     * Enrich headers list with an item.
     *
     * @param array $headers
     * @param Item  $item
     */
    private function enrichHeadersWithItem(
        array &$headers,
        Item $item
    ) {
        $this->enrichHeaderWithFields($headers, 'metadata', $item->getMetadata());
        $this->enrichHeaderWithFields($headers, 'indexed_metadata', $item->getIndexedMetadata());
        $this->enrichHeaderWithFields($headers, 'searchable_metadata', $item->getSearchableMetadata());
    }

    /**
     * Enrich headers list with an item.
     *
     * @param array  $headers
     * @param string $section
     * @param array  $fields
     */
    private function enrichHeaderWithFields(
        array &$headers,
        string $section,
        array $fields
    ) {
        foreach ($fields as $fieldName => $_) {
            if (!in_array($fieldName, $headers[$section])) {
                $headers[$section][] = $fieldName;
            }
        }
    }

    /**
     * Parse fields from item having header.
     *
     * @param array $header
     * @param array $item
     *
     * @return array
     */
    private function parseFieldsFromHeaderAndItemAsArray(
        array $header,
        array $item
    ) {
        return [
            'metadata' => $this->parseSectionFromHeaderAndItemAsArray($header, 'metadata', $item),
            'indexed_metadata' => $this->parseSectionFromHeaderAndItemAsArray($header, 'indexed_metadata', $item),
            'searchable_metadata' => $this->parseSectionFromHeaderAndItemAsArray($header, 'searchable_metadata', $item),
        ];
    }

    /**
     * Parse section fields from item having header.
     *
     * @param array  $header
     * @param string $section
     * @param array  $item
     *
     * @return array
     */
    private function parseSectionFromHeaderAndItemAsArray(
        array $header,
        string $section,
        array $item
    ) {
        return array_intersect_key(
            $item,
            array_flip($header[$section] ?? [])
        );
    }
}
