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

use Apisearch\Config\SynonymReader;
use Apisearch\Exception\SynonymsException;
use PHPUnit\Framework\TestCase;

/**
 * Class SynonymReaderTest.
 */
class SynonymReaderTest extends TestCase
{
    /**
     * Test non existing file.
     */
    public function testNonExistingFile()
    {
        $synonymReader = new SynonymReader();
        $this->expectException(SynonymsException::class);
        $synonymReader->readSynonymsFromFile(__DIR__.'/nonexistingfile276892.csv');
    }

    /**
     * Test empty file.
     */
    public function testEmptyFile()
    {
        $synonymReader = new SynonymReader();
        $this->assertCount(
            0,
            $synonymReader->readSynonymsFromFile(__DIR__.'/empty.csv')
        );
    }

    /**
     * Test synonyms.
     */
    public function testSynonymsFile()
    {
        $synonymReader = new SynonymReader();
        $synonyms = $synonymReader->readSynonymsFromFile(__DIR__.'/synonyms.csv');
        $this->assertCount(
            2,
            $synonyms
        );
        $this->assertEquals(
            'anothersynonym1',
            $synonyms[0]->getWords()[2]
        );
    }

    /**
     * Test empty array.
     */
    public function testEmptyArray()
    {
        $synonymReader = new SynonymReader();
        $this->assertCount(
            0,
            $synonymReader->readSynonymsFromCommaSeparatedArray([])
        );
    }

    /**
     * Test empty array of comma separated.
     */
    public function testCommaSeparatedArray()
    {
        $synonymReader = new SynonymReader();
        $this->assertCount(
            2,
            $synonymReader->readSynonymsFromCommaSeparatedArray([
                'word1, synonym1,  anothersynonym1',
                'word2, synonym2',
                'word3',
            ])
        );
    }
}
