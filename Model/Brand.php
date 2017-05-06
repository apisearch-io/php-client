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

use Puntmig\Search\Exception\ModelException;

/**
 * Class Brand.
 */
class Brand implements HttpTransportable
{
    /**
     * @var string
     *
     * Name
     */
    const TYPE = 'brand';

    /**
     * @var BrandReference
     *
     * Brand reference
     */
    private $brandReference;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var string
     *
     * Slug
     */
    private $slug;

    /**
     * @var string
     *
     * First level searchable data
     */
    private $firstLevelSearchableData;

    /**
     * Brand constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $slug
     *
     * @throws ModelException
     */
    public function __construct(
        string $id,
        string $name,
        string $slug
    ) {
        if (
            empty($id) ||
            empty($name)
        ) {
            throw new ModelException('A brand should always have, at least, and ID and a name');
        }

        $this->brandReference = new BrandReference($id);
        $this->name = $name;
        $this->slug = $slug;
        $this->firstLevelSearchableData = $name;
    }

    /**
     * Get brand reference.
     *
     * @return BrandReference
     */
    public function getBrandReference() : BrandReference
    {
        return $this->brandReference;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->brandReference->getId();
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
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
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return static
     */
    public static function createFromArray(array $array)
    {
        if (
            !isset($array['id']) ||
            !isset($array['name']) ||
            !isset($array['slug'])
        ) {
            return null;
        }

        return new static(
            (string) $array['id'],
            (string) $array['name'],
            (string) $array['slug']
        );
    }
}
