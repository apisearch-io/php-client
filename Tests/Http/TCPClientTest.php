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

use Apisearch\Exception\ConnectionException;
use Apisearch\Http\HttpAdapter;
use Apisearch\Http\RetryMap;
use Apisearch\Http\TCPClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Class TCPClientTest.
 */
class TCPClientTest extends TestCase
{
    /**
     * Test n query retries.
     */
    public function testRequestTries()
    {
        $this->makeClientTest(
            [['url' => '/items', 'method' => 'get', 'retries' => 2, 'microseconds_between_retries' => 100000]],
            '/items',
            'get',
            3,
            200000,
            null
        );

        $this->makeClientTest(
            [['url' => 'items/', 'method' => 'GET', 'retries' => 2, 'microseconds_between_retries' => 100000]],
            '/items',
            'get',
            3,
            200000,
            null
        );

        $this->makeClientTest(
            [['url' => '/items/', 'method' => 'GET ', 'retries' => 2, 'microseconds_between_retries' => 100000]],
            '/items',
            'get',
            3,
            200000,
            null
        );

        $this->makeClientTest(
            [['url' => '*', 'method' => '*', 'retries' => 2, 'microseconds_between_retries' => 100000]],
            '/items',
            'get',
            3,
            200000,
            null
        );

        $this->makeClientTest(
            [['url' => '*', 'method' => 'post', 'retries' => 10, 'microseconds_between_retries' => 1000000]],
            '/items',
            'get',
            1,
            null,
            1000000
        );

        $this->makeClientTest(
            [['url' => '/items/v2', 'method' => 'get', 'retries' => 10, 'microseconds_between_retries' => 1000000]],
            '/items',
            'get',
            1,
            null,
            1000000
        );

        $this->makeClientTest(
            [['url' => '/items/v2', 'method' => 'get', 'retries' => 10, 'microseconds_between_retries' => 1000000]],
            '/items',
            'get',
            1,
            null,
            1000000
        );
    }

    /**
     * Test client.
     *
     * @param array  $retryMap
     * @param string $url
     * @param string $method
     * @param int    $triesExpected
     * @param int    $microsecondsMinimumExpected
     * @param int    $microsecondsMaximumExpected
     */
    private function makeClientTest(
         array $retryMap,
         string $url,
         string $method,
         int $triesExpected,
         ?int $microsecondsMinimumExpected,
         ?int $microsecondsMaximumExpected
    ) {
        $httpAdapter = $this->prophesize(HttpAdapter::class);
        $before = microtime(true) * 1000000;
        $httpAdapter
            ->getByRequestParts(Argument::cetera())
            ->shouldBeCalledTimes($triesExpected)
            ->willThrow(ConnectionException::class);

        $apisearchClient = new TCPClient(
            'http://localhost:9999',
            $httpAdapter->reveal(),
            'v1',
            RetryMap::createFromArray($retryMap)
        );

        try {
            $apisearchClient->get($url, $method);
            $this->fail('Client should throw an exception of type '.ConnectionException::class);
        } catch (ConnectionException $e) {
            $after = microtime(true) * 1000000;
            $diff = $after - $before;
            if (!is_null($microsecondsMinimumExpected)) {
                $this->assertTrue($diff >= $microsecondsMinimumExpected);
            }

            if (!is_null($microsecondsMaximumExpected)) {
                $this->assertTrue($diff <= $microsecondsMaximumExpected);
            }
        }
    }
}
