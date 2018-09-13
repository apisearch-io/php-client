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

use Apisearch\Config\Config;
use Apisearch\Config\Synonym;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigTest.
 */
class ConfigTest extends TestCase
{
    /**
     * Test object construction.
     */
    public function testCreate()
    {
        $config = new Config('es', true);
        $this->assertEquals('es', $config->getLanguage());
        $this->assertTrue($config->shouldSearchableMetadataBeStored());
    }

    /**
     * Test null language.
     */
    public function testCreateNullLanguage()
    {
        $config = new Config(null, true);
        $this->assertNull($config->getLanguage());
    }

    /**
     * Test null language.
     */
    public function testCreateSearchableMetadataStoreDisabled()
    {
        $config = new Config(null, false);
        $this->assertFalse($config->shouldSearchableMetadataBeStored());

        $config = Config::createFromArray([
            'store_searchable_metadata' => false,
        ]);
        $this->assertFalse($config->shouldSearchableMetadataBeStored());
    }

    /**
     * Test synonyms.
     */
    public function testSynonyms()
    {
        $config = new Config(null, true);
        $config->addSynonym(Synonym::createByWords(['a', 'b']));
        $config->addSynonym(Synonym::createByWords(['c', 'd']));
        $this->assertEquals(
            [
                Synonym::createByWords(['a', 'b']),
                Synonym::createByWords(['c', 'd']),
            ],
            $config->getSynonyms()
        );
    }

    /**
     * Test default values.
     */
    public function testDefaultValues()
    {
        $config = Config::createFromArray([]);
        $this->assertNull($config->getLanguage());
        $this->assertTrue($config->shouldSearchableMetadataBeStored());
        $this->assertEmpty($config->getSynonyms());
    }

    /**
     * Test http transport.
     */
    public function testHttpTransport()
    {
        $config = [
            'language' => 'es',
            'store_searchable_metadata' => false,
            'synonyms' => [
                ['words' => ['a', 'b']],
                ['words' => ['c', 'd']],
            ],
        ];

        $this->assertEquals(
            $config,
            Config::createFromArray($config)->toArray()
        );
    }

    /**
     * Test http transport.
     */
    public function testHttpTransportDefaultParameters()
    {
        $config = [
            'language' => null,
            'store_searchable_metadata' => true,
            'synonyms' => [],
        ];

        $this->assertEquals(
            [],
            Config::createFromArray($config)->toArray()
        );
    }
}
