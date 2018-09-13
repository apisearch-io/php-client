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

namespace Apisearch\Config;

/**
 * Class SynonymReader.
 */
class SynonymReader
{
    /**
     * Read synonyms from file.
     *
     * @param string $filepath
     *
     * @return Synonym[]
     */
    public function readSynonymsFromFile(string $filepath): array
    {
        if (
            !is_file($filepath) ||
            !is_readable($filepath)
        ) {
            return [];
        }

        $data = file_get_contents($filepath);

        if (!is_string($data)) {
            return [];
        }

        return array_filter(array_map(function ($line) {
            if (!is_string($line)) {
                return false;
            }

            $words = str_getcsv($line, ',');

            if (count($words) <= 1) {
                return false;
            }

            return Synonym::createByWords($words);
        }, str_getcsv(file_get_contents($filepath), "\n")));
    }

    /**
     * Read synonyms from comma separated array.
     *
     * @param array $synonymsAsCommaSeparatedArray
     *
     * @return Synonym[]
     */
    public function readSynonymsFromCommaSeparatedArray(array $synonymsAsCommaSeparatedArray): array
    {
        return array_filter(array_map(function (string $line) {
            if (empty($line)) {
                return false;
            }

            $words = explode(',', $line);

            if (count($words) <= 1) {
                return false;
            }

            return Synonym::createByWords($words);
        }, $synonymsAsCommaSeparatedArray));
    }
}
