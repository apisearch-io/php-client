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

namespace Apisearch\Tests\Transformer;

use DateTime;

/**
 * File header placeholder.
 */
class Product
{
    /**
     * @var string
     *
     * SKU
     */
    private $sku;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var DateTime
     *
     * Created at
     */
    private $createdAt;

    /**
     * Product constructor.
     *
     * @param string   $sku
     * @param string   $name
     * @param DateTime $createdAt
     */
    public function __construct(
        string $sku,
        string $name,
        DateTime $createdAt
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->createdAt = $createdAt;
    }

    /**
     * Get Sku.
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get CreatedAt.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
