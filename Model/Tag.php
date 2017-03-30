<?php

/*
 * This file is part of the Search PHP Library.
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

namespace Puntmig\Search\Model;

/**
 * Class Tag.
 */
class Tag implements HttpTransportable
{
    /**
     * @var string
     *
     * Name
     */
    const TYPE = 'tag';

    /**
     * @var TagReference
     *
     * Tag reference
     */
    private $tagReference;

    /**
     * @var string
     *
     * First level searchable data
     */
    private $firstLevelSearchableData;

    /**
     * Category constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->tagReference = new TagReference($name);
        $this->firstLevelSearchableData = $name;
    }

    /**
     * Get reference.
     *
     * @return TagReference
     */
    public function getTagReference()
    {
        return $this->tagReference;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this
            ->tagReference
            ->getName();
    }

    /**
     * Get first level searchable data.
     *
     * @return string
     */
    public function getFirstLevelSearchableData(): string
    {
        return $this->firstLevelSearchableData;
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     */
    public static function createFromArray(array $array) : self
    {
        if (!isset($array['name'])) {
            return null;
        }

        return new static(
            (string) $array['name']
        );
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }
}
