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

namespace Apisearch\Tests\Transformer;

use Apisearch\Transformer\Transformer;
use DateTime;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $transformer = new Transformer($this->createMock(EventDispatcherInterface::class));
        $transformer->addReadTransformer(new ProductReadTransformer());
        $transformer->addWriteTransformer(new ProductWriteTransformer());
        $product = new Product('34672864', 'zapatilla', new DateTime());
        $item = $transformer->toItem($product);
        $this->assertEquals(
            $item,
            $transformer->toItems([$product])[0]
        );
        $itemUUID = $transformer->toItemUUID($product);
        $this->assertEquals(
            $itemUUID,
            $transformer->toItemUUIDs([$product])[0]
        );
        $this->assertSame('34672864', $itemUUID->getId());
        $this->assertSame('product', $itemUUID->getType());

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
     * Test basic without read transformer.
     */
    public function testBasicWithoutReadTransformer()
    {
        $transformer = new Transformer($this->createMock(EventDispatcherInterface::class));
        $transformer->addWriteTransformer(new ProductWriteTransformer());
        $product = new Product('34672864', 'zapatilla', new DateTime());
        $item = $transformer->toItem($product);

        $this->assertSame(
            $item,
            $transformer->fromItem($item)
        );
    }
}
