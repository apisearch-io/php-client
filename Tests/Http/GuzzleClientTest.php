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

namespace Apisearch\Tests\Http;

use Apisearch\Http\GuzzleClient;
use Apisearch\Http\RetryMap;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

/**
 * Class GuzzleClientTest.
 */
class GuzzleClientTest extends PHPUnit_Framework_TestCase
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
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->willBeConstructedWith([
            'defaults' => [
                'timeout' => 5,
                'http_errors' => false,
            ],
        ]);

        $before = microtime(true) * 1000000;
        $guzzleClient
            ->get(Argument::cetera())
            ->shouldBeCalledTimes($triesExpected)
            ->willThrow(ConnectException::class);

        $apisearchClient = new GuzzleClient(
            $guzzleClient->reveal(),
            'http://localhost:9999',
            'v1',
            RetryMap::createFromArray($retryMap)
        );

        try {
            $apisearchClient->get($url, $method);
            $this->fail('Client should throw an exception of type '.ConnectException::class);
        } catch (ConnectException $e) {
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
