<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Tests\Query;

use PHPUnit_Framework_TestCase;

use Puntmig\Search\Model\Product;

/**
 * Class ProductTest.
 */
class ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test optional parameters for product.
     */
    public function testOptionalParameters()
    {
        $product = Product::createFromArray([
            'id' => '123',
            'family' => 'book',
            'ean' => '3467824534',
            'name' => 'my book',
            'slug' => 'my-book',
            'description' => 'description',
            'price' => 7894.98,
            'currency' => 'EUR',
        ]);
        $productAsArray = $product->toArray();
        $product = Product::createFromArray($productAsArray);

        $this->assertEquals('', $product->getLongDescription());
        $this->assertNull($product->getReducedPrice());
        $this->assertNull($product->getStock());
        $this->assertNull($product->getManufacturer());
        $this->assertNull($product->getBrand());
        $this->assertEquals('', $product->getImage());
        $this->assertNull($product->getRating());
        $this->assertInstanceof('DateTime', $product->getUpdatedAt());
        $this->assertNull($product->getCoordinate());
    }

    /**
     * Test setters.
     */
    public function testPriceManipulation()
    {
        $product = Product::createFromArray([
            'id' => '123',
            'family' => 'book',
            'ean' => '3467824534',
            'name' => 'my book',
            'slug' => 'my-book',
            'description' => 'description',
            'price' => 7894.98,
            'currency' => 'EUR',
        ]);

        $product->setPrice(100.0);
        $this->assertNull($product->getReducedPrice());
        $product->setReducedPrice(50.0);
        $this->assertEquals(
            50.0,
            $product->getRealPrice()
        );
        $this->assertEquals(
            50.0,
            $product->getDiscount()
        );
        $this->assertEquals(
            50,
            $product->getDiscountPercentage()
        );
        $product->setPrice(20.0);
        $this->assertEquals(
            0.0,
            $product->getDiscount()
        );
        $this->assertEquals(
            0,
            $product->getDiscountPercentage()
        );
        $productAsArray = $product->toArray();
        $productAsArray['reduced_price'] = 10.0;
        $product = Product::createFromArray($productAsArray);
        $this->assertEquals(
            10.0,
            $product->getDiscount()
        );
        $this->assertEquals(
            50,
            $product->getDiscountPercentage()
        );
    }
}
