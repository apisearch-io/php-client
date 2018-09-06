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

use Apisearch\Http\Endpoints;
use PHPUnit\Framework\TestCase;

/**
 * Class EndpointsTest.
 */
class EndpointsTest extends TestCase
{
    /**
     * All endoints.
     */
    private $allEndpoints = [
        'v1-index-create',
        'v1-index-delete',
        'v1-index-reset',
        'v1-index-config',

        'v1-items-index',
        'v1-items-delete',

        'v1-query',
        'v1-events',
        'v1-events-stream',
        'v1-logs',
        'v1-logs-stream',

        'v1-token-add',
        'v1-token-delete',
        'v1-tokens-get',
        'v1-tokens-delete',

        'v1-interaction',
        'v1-interactions-delete',
    ];

    /**
     * All composed endpoints.
     */
    private $allComposedEndpoints = [
        'put~~/v1/index',
        'delete~~/v1/index',
        'post~~/v1/index/reset',
        'post~~/v1/index',

        'post~~/v1/items',
        'delete~~/v1/items',

        'get~~/v1',
        'get~~/v1/events',
        'get~~/v1/events/stream',
        'get~~/v1/logs',
        'get~~/v1/logs/stream',

        'post~~/v1/token',
        'delete~~/v1/token',
        'get~~/v1/tokens',
        'delete~~/v1/tokens',

        'get~~/v1/interaction',
        'delete~~/v1/interactions',
    ];

    /**
     * Test compose.
     */
    public function testCompose()
    {
        $this->assertEquals(
            $this->allComposedEndpoints,
            Endpoints::compose(array_merge(
                ['v1-non-existing'],
                $this->allEndpoints
            ))
        );
    }

    /**
     * Test compose.
     */
    public function testFromComposed()
    {
        $this->assertEquals(
            $this->allEndpoints,
            Endpoints::fromComposed(array_merge(
                ['get~~/v1/non/existing'],
                $this->allComposedEndpoints
            ))
        );
    }
}
