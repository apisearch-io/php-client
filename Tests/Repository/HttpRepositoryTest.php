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
use Apisearch\Model\AppUUID;
use Apisearch\Model\IndexUUID;
use Apisearch\Model\TokenUUID;
use Apisearch\Query\Query;
use Apisearch\Repository\HttpRepository;
use Apisearch\Repository\RepositoryReference;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Class HttpRepositoryTest.
 */
class HttpRepositoryTest extends TestCase
{
    /**
     * Test add, delete and query items by UUID.
     */
    public function testNullResponse()
    {
        $client = $this->prophesize(HttpClient::class);
        $client->get(Argument::cetera())->willReturn(['code' => 0, 'body' => null]);
        $repository = new HttpRepository($client->reveal());
        $repository->setCredentials(RepositoryReference::create(AppUUID::createById('123'), IndexUUID::createById('456')), TokenUUID::createById('000'));
        $this->expectException(ConnectionException::class);
        $repository->query(Query::createMatchAll());
    }

    /**
     * Test add, delete and query items by UUID.
     */
    public function testConnectionExceptionResponse()
    {
        $client = $this->prophesize(HttpClient::class);
        $client->get(Argument::cetera())->willThrow(ConnectionException::buildConnectExceptionByUrl('http://xxx.xx'));
        $repository = new HttpRepository($client->reveal());
        $repository->setCredentials(RepositoryReference::create(AppUUID::createById('123'), IndexUUID::createById('456')), TokenUUID::createById('000'));
        $this->expectException(ConnectionException::class);
        $repository->query(Query::createMatchAll());
    }
}
