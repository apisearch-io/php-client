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

use Apisearch\Http\RequestParts;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestPartsTest.
 */
class RequestPartsTest extends TestCase
{
    /**
     * Test constructor.
     */
    public function testConstruct()
    {
        $requestParts = new RequestParts(
            'url',
            ['param1' => 'value1'],
            ['option1' => 'op1']
        );

        $this->assertEquals('url', $requestParts->getUrl());
        $this->assertEquals(['param1' => 'value1'], $requestParts->getParameters());
        $this->assertEquals(['option1' => 'op1'], $requestParts->getOptions());
    }
}
