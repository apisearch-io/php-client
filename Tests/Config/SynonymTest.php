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

namespace Apisearch\Tests\Config;

use Apisearch\Config\Synonym;
use PHPUnit_Framework_TestCase;

class SynonymTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string[]
     *
     * Words
     */
    private $words = ['a', 'b', 'c'];

    /**
     * Test creation by array.
     */
    public function testCreate()
    {
        $synonym = Synonym::createByWords($this->words);
        $this->assertEquals(
            $this->words,
            $synonym->getWords()
        );
    }

    /**
     * Test expand.
     */
    public function testExpand()
    {
        $synonym = Synonym::createByWords($this->words);
        $this->assertEquals(
            'a,b,c',
            $synonym->expand()
        );
    }

    /**
     * Test create from array.
     */
    public function testCreateFromArray()
    {
        $synonym = Synonym::createFromArray(['words' => $this->words]);
        $this->assertEquals(
            ['words' => $this->words],
            $synonym->toArray()
        );
    }
}
