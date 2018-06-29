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

namespace Apisearch\Tests\Exception;

use Apisearch\Exception\TransportableException;
use Apisearch\Exception\UnsupportedContentTypeException;
use PHPUnit\Framework\TestCase;

/**
 * Class UnsupportedContentTypeExceptionTest.
 */
class UnsupportedContentTypeExceptionTest extends TestCase
{
    /**
     * Assert that returns proper transportable error.
     */
    public function testTransportableErrorCode()
    {
        $this->assertInstanceOf(
            TransportableException::class,
            new UnsupportedContentTypeException()
        );

        $this->assertEquals(
            415,
            UnsupportedContentTypeException::getTransportableHTTPError()
        );
    }

    /**
     * Assert that extends an exception.
     */
    public function testExtendsException()
    {
        $this->assertInstanceOf(
            \Exception::class,
            new UnsupportedContentTypeException()
        );
    }
}
