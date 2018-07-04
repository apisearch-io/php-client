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

namespace Apisearch\Tests\Http;

use Apisearch\Http\Retry;
use PHPUnit\Framework\TestCase;

/**
 * Class RetryTest.
 */
class RetryTest extends TestCase
{
    /**
     * Test construct.
     */
    public function testConstruct()
    {
        $retry = new Retry(
            'myurl',
            'get',
            10,
            100
        );

        $this->assertEquals('myurl', $retry->getUrl());
        $this->assertEquals('get', $retry->getMethod());
        $this->assertEquals(10, $retry->getRetries());
        $this->assertEquals(100, $retry->getMicrosecondsBetweenRetries());
    }

    /**
     * Test construct.
     */
    public function testCreateFromArray()
    {
        $retryAsArray = [
            'url' => '/myurl/',
            'method' => 'get',
            'retries' => 10,
            'microseconds_between_retries' => 100,
        ];

        $retry = Retry::createFromArray($retryAsArray);
        $this->assertEquals('myurl', $retry->getUrl());
        $this->assertEquals('get', $retry->getMethod());
        $this->assertEquals(10, $retry->getRetries());
        $this->assertEquals(100, $retry->getMicrosecondsBetweenRetries());
    }

    /**
     * Test construct.
     */
    public function testCreateFromArrayDefaults()
    {
        $retryAsArray = [];

        $retry = Retry::createFromArray($retryAsArray);
        $this->assertEquals('*', $retry->getUrl());
        $this->assertEquals('*', $retry->getMethod());
        $this->assertEquals(0, $retry->getRetries());
        $this->assertEquals(Retry::DEFAULT_MICROSECONDS_BETWEEN_RETRIES, $retry->getMicrosecondsBetweenRetries());
    }
}
