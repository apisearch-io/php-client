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

namespace Puntmig\Search\Tests\Transformer;

use DateTime;
use PHPUnit_Framework_TestCase;

use Puntmig\Search\Transformer\Transformer;

/**
 * Class TransformerTest.
 */
class TransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic transformer.
     */
    public function testBasic()
    {
        $transformer = new Transformer();
        $transformer->addReadTransformer(new ProductReadTransformer());
        $transformer->addWriteTransformer(new ProductWriteTransformer());
        $product = new Product('34672864', 'zapatilla', new DateTime());
        $item = $transformer->toItem($product);
        $this->assertEquals(
            $item,
            $transformer->toItems([$product])[0]
        );

        $returnedProduct = $transformer->fromItem($item);
        $this->assertEquals(
            $product->getCreatedAt()->format(DATE_ATOM),
            $returnedProduct->getCreatedAt()->format(DATE_ATOM)
        );
        $this->assertEquals(
            $product->getSku(),
            $returnedProduct->getSku()
        );
        $this->assertEquals(
            $product->getName(),
            $returnedProduct->getName()
        );
        $this->assertEquals(
            $product->getSku(),
            $transformer->fromItems([$item])[0]->getSku()
        );
    }

    /**
     * Test basic without write transformer.
     *
     * @expectedException \Puntmig\Search\Exception\TransformerException
     */
    public function testBasicWithoutWriteTransformer()
    {
        $transformer = new Transformer();
        $transformer->addReadTransformer(new ProductReadTransformer());
        $product = new Product('34672864', 'zapatilla', new DateTime());
        $transformer->toItems([$product]);
    }

    /**
     * Test basic without read transformer.
     */
    public function testBasicWithoutReadTransformer()
    {
        $transformer = new Transformer();
        $transformer->addWriteTransformer(new ProductWriteTransformer());
        $product = new Product('34672864', 'zapatilla', new DateTime());
        $item = $transformer->toItem($product);

        $this->assertSame(
            $item,
            $transformer->fromItem($item)
        );
    }
}
