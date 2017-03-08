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

use DateTime;

/**
 * Class Product.
 */
class Product implements HttpTransportable
{
    /**
     * @var string
     *
     * Name
     */
    const TYPE = 'product';

    /**
     * @var string
     *
     * Id
     */
    private $id;

    /**
     * @var string
     *
     * family
     */
    private $family;

    /**
     * @var string
     *
     * EAN
     */
    private $ean;

    /**
     * @var string
     *
     * Name
     */
    private $name;

    /**
     * @var string
     *
     * Description
     */
    private $description;

    /**
     * @var string
     *
     * Long description
     */
    private $longDescription;

    /**
     * @var int
     *
     * price
     */
    private $price;

    /**
     * @var int
     *
     * Reduced price
     */
    private $reducedPrice;

    /**
     * @var int
     *
     * Stock
     */
    private $stock;

    /**
     * @var Manufacturer
     *
     * Manufacturer
     */
    private $manufacturer;

    /**
     * @var Brand
     *
     * Brand
     */
    private $brand;

    /**
     * @var Category[]
     *
     * Categories
     */
    private $categories;

    /**
     * @var Tag[]
     *
     * Tags
     */
    private $tags;

    /**
     * @var string
     *
     * Image
     */
    private $image;

    /**
     * @var float
     *
     * Rating
     */
    private $rating;

    /**
     * @var string
     *
     * First level searchable data
     */
    private $firstLevelSearchableData;

    /**
     * @var string
     *
     * Second level searchable data
     */
    private $secondLevelSearchableData;

    /**
     * @var DateTime
     *
     * Updated at
     */
    private $updatedAt;

    /**
     * Product constructor.
     *
     * @param string            $id
     * @param string            $family
     * @param string            $ean
     * @param string            $name
     * @param string            $description
     * @param null|string       $longDescription
     * @param float             $price
     * @param null|float        $reducedPrice
     * @param null|int          $stock
     * @param null|Manufacturer $manufacturer
     * @param null|Brand        $brand
     * @param null|string       $image
     * @param null|float        $rating
     * @param null|DateTime     $updatedAt
     */
    public function __construct(
        string $id,
        string $family,
        string $ean,
        string $name,
        string $description,
        ? string $longDescription,
        float $price,
        ? float $reducedPrice,
        ? int $stock,
        ? Manufacturer $manufacturer,
        ? Brand $brand,
        ? string $image,
        ? float $rating,
        ? DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->family = $family;
        $this->ean = $ean;
        $this->name = $name;
        $this->description = $description;
        $this->longDescription = ($longDescription ?? '');
        $this->price = $price;
        $this->reducedPrice = ($reducedPrice ?? $price);
        $this->stock = $stock;
        $this->manufacturer = $manufacturer;
        $this->brand = $brand;
        $this->categories = [];
        $this->tags = [];
        $this->image = ($image ?? '');
        $this->rating = !is_null($rating)
            ? round($rating, 1)
            : null;
        $this->updatedAt = ($updatedAt ?? new DateTime());

        $this->firstLevelSearchableData = $name;
        if ($manufacturer instanceof Manufacturer) {
            $this->firstLevelSearchableData .= " {$manufacturer->getName()}";
        }
        if ($brand instanceof Brand) {
            $this->firstLevelSearchableData .= " {$brand->getName()}";
        }
        $this->secondLevelSearchableData = "$description $longDescription";
    }

    /**
     * Get product id.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get family.
     *
     * @return string
     */
    public function getFamily() : string
    {
        return $this->family;
    }

    /**
     * Get EAN.
     *
     * @return string
     */
    public function getEan() : string
    {
        return $this->ean;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Get long description.
     *
     * @return string
     */
    public function getLongDescription() : string
    {
        return $this->longDescription;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice() : float
    {
        return $this->price;
    }

    /**
     * Get reduced price.
     *
     * @return float
     */
    public function getReducedPrice() : float
    {
        return $this->reducedPrice;
    }

    /**
     * Get real price.
     *
     * @return float
     */
    public function getRealPrice() : float
    {
        return min(
            $this->price,
            $this->reducedPrice
        );
    }

    /**
     * Get discount.
     *
     * @return float
     */
    public function getDiscount() : float
    {
        return $this->price - $this->getRealPrice();
    }

    /**
     * Get discount percentage.
     *
     * @return int
     */
    public function getDiscountPercentage() : int
    {
        return (int) round(100 * $this->getDiscount() / $this->getPrice());
    }

    /**
     * Get stock.
     *
     * @return null|int
     */
    public function getStock(): ? int
    {
        return $this->stock;
    }

    /**
     * Get manufacturer.
     *
     * @return null|Manufacturer
     */
    public function getManufacturer() : ? Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * Get brand.
     *
     * @return null|Brand
     */
    public function getBrand() : ? Brand
    {
        return $this->brand;
    }

    /**
     * Add Category.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
        $this->firstLevelSearchableData .= " {$category->getName()}";
    }

    /**
     * Get categories.
     *
     * @return Category[]
     */
    public function getCategories() : array
    {
        return $this->categories;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        if (isset($this->tags[$tag->getName()])) {
            return;
        }

        $this->tags[$tag->getName()] = $tag;
        $this->firstLevelSearchableData .= " {$tag->getName()}";
    }

    /**
     * Get tags.
     *
     * @return Tag[]
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * Get image.
     *
     * @return null|string
     */
    public function getImage(): ? string
    {
        return $this->image;
    }

    /**
     * Get rating.
     *
     * @return null|float
     */
    public function getRating() : ? float
    {
        return $this->rating;
    }

    /**
     * Get Updated at.
     *
     * @return null|DateTime
     */
    public function getUpdatedAt() : ? DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Get first level searchable data.
     *
     * @return string
     */
    public function getFirstLevelSearchableData() : string
    {
        return $this->firstLevelSearchableData;
    }

    /**
     * Get second level searchable data.
     *
     * @return string
     */
    public function getSecondLevelSearchableData(): string
    {
        return $this->secondLevelSearchableData;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        $array = [
            'id' => $this->id,
            'family' => $this->family,
            'ean' => $this->ean,
            'name' => $this->name,
            'description' => $this->description,
            'long_description' => $this->longDescription,
            'price' => $this->price,
            'reduced_price' => $this->reducedPrice,
            'stock' => $this->stock,
            'image' => $this->image,
            'rating' => $this->rating,
            'updated_at' => is_null($this->updatedAt)
                ? null
                : $this->updatedAt->format(DATE_ATOM),
            'categories' => array_map(function (Category $category) {
                return $category->toArray();
            }, $this->categories),
            'tags' => array_map(function (Tag $tag) {
                return $tag->toArray();
            }, $this->tags),
        ];

        if ($this->manufacturer instanceof Manufacturer) {
            $array['manufacturer'] = $this
                ->manufacturer
                ->toArray();
        }

        if ($this->brand instanceof Brand) {
            $array['brand'] = $this
                ->brand
                ->toArray();
        }

        return $array;
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
        $product = new self(
            (string) $array['id'],
            (string) $array['family'],
            (string) $array['ean'],
            (string) $array['name'],
            (string) $array['description'],
            $array['long_description'] ?? null,
            (float) $array['price'],
            isset($array['reduced_price'])
                ? ((float) $array['reduced_price'])
                : null,
            isset($array['stock'])
                ? ((int) $array['stock'])
                : null,
            isset($array['manufacturer'])
                ? Manufacturer::createFromArray($array['manufacturer'])
                : null,
            isset($array['brand'])
                ? Brand::createFromArray($array['brand'])
                : null,
            $array['image'] ?? null,
            isset($array['rating'])
                ? ((float) $array['rating'])
                : null,
            isset($array['updated_at'])
                ? DateTime::createFromFormat(DATE_ATOM, $array['updated_at'])
                : null
        );

        if (
            isset($array['categories']) &&
            is_array($array['categories'])
        ) {
            foreach ($array['categories'] as $category) {
                $product->addCategory(
                    Category::createFromArray($category)
                );
            }
        }

        if (
            isset($array['tags']) &&
            is_array($array['tags'])
        ) {
            foreach ($array['tags'] as $tag) {
                $product->addTag(
                    Tag::createFromArray($tag)
                );
            }
        }

        return $product;
    }
}
