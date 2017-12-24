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

use Apisearch\Model\Item;

/**
 * Exporter.
 */
interface Exporter
{
    /**
     * Get parser name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Convert array of Items to string format.
     *
     * @param Item[] $items
     *
     * @return string
     */
    public function itemsToFormat(array $items): string;

    /**
     * Convert string formatted to array of Items.
     *
     * @param string $data
     *
     * @return Item[]
     */
    public function formatToItems(string $data): array;
}
