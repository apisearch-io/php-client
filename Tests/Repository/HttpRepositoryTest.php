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

namespace Apisearch\Tests\Repository;

use Apisearch\Exception\ConnectionException;
use Apisearch\Http\HttpClient;
use Apisearch\Query\Query;
use Apisearch\Repository\HttpRepository;
use Apisearch\Repository\RepositoryReference;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

/**
 * Class HttpRepositoryTest.
 */
class HttpRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test add, delete and query items by UUID.
     *
     * @expectedException \Apisearch\Exception\ConnectionException
     */
    public function testNullResponse()
    {
        $client = $this->prophesize(HttpClient::class);
        $client->get(Argument::cetera())->willReturn(['code' => 0, 'body' => null]);
        $repository = new HttpRepository($client->reveal());
        $repository->setCredentials(RepositoryReference::create('123', '456'), '000');
        $repository->query(Query::createMatchAll());
    }

    /**
     * Test add, delete and query items by UUID.
     *
     * @expectedException \Apisearch\Exception\ConnectionException
     */
    public function testConnectionExceptionResponse()
    {
        $client = $this->prophesize(HttpClient::class);
        $client->get(Argument::cetera())->willThrow(ConnectionException::buildConnectExceptionByUrl('http://xxx.xx'));
        $repository = new HttpRepository($client->reveal());
        $repository->setCredentials(RepositoryReference::create('123', '456'), '000');
        $repository->query(Query::createMatchAll());
    }
}
