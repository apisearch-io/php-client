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

namespace Apisearch\Tests\Config;

use Apisearch\Config\ImmutableConfig;
use Apisearch\Config\Synonym;
use PHPUnit_Framework_TestCase;

/**
 * File header placeholde.
 */
class ImmutableConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test object construction.
     */
    public function testCreate()
    {
        $config = new ImmutableConfig('es', true);
        $this->assertEquals('es', $config->getLanguage());
        $this->assertTrue($config->shouldSearchableMetadataBeStored());
    }

    /**
     * Test null language.
     */
    public function testCreateNullLanguage()
    {
        $config = new ImmutableConfig(null, true);
        $this->assertNull($config->getLanguage());
    }

    /**
     * Test null language.
     */
    public function testCreateSearchableMetadataStoreDisabled()
    {
        $config = new ImmutableConfig(null, false);
        $this->assertFalse($config->shouldSearchableMetadataBeStored());

        $config = ImmutableConfig::createFromArray([
            'store_searchable_metadata' => false,
        ]);
        $this->assertFalse($config->shouldSearchableMetadataBeStored());
    }

    /**
     * Test synonyms.
     */
    public function testSynonyms()
    {
        $config = new ImmutableConfig(null, true);
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
        $config = ImmutableConfig::createFromArray([]);
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
            ImmutableConfig::createFromArray($config)->toArray()
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
            ImmutableConfig::createFromArray($config)->toArray()
        );
    }
}
