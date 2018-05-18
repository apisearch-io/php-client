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

namespace Apisearch\Config;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * Class Synonym.
 */
class Synonym implements HttpTransportable
{
    /**
     * @var string[]
     *
     * Synonyms
     */
    private $words;

    /**
     * Synonym constructor.
     *
     * @param string[] $words
     */
    private function __construct(array $words)
    {
        $this->words = $words;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['words' => $this->words];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        return new self($array['words'] ?? []);
    }

    /**
     * Expand.
     *
     * @return string
     */
    public function expand(): string
    {
        return implode(', ', $this->words);
    }
}
