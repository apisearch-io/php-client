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
     * Slug
     */
    private $slug;

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
     * @var string
     *
     * Currency
     */
    private $currency;

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
     * @var Coordinate
     *
     * Coordinate
     */
    private $coordinate;

    /**
     * @var float
     *
     * Distance
     */
    private $distance;

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
     * @param string            $slug
     * @param string            $description
     * @param null|string       $longDescription
     * @param float             $price
     * @param string            $currency
     * @param null|float        $reducedPrice
     * @param null|int          $stock
     * @param null|Manufacturer $manufacturer
     * @param null|Brand        $brand
     * @param null|string       $image
     * @param null|float        $rating
     * @param null|DateTime     $updatedAt
     * @param null|Coordinate   $coordinate
     */
    public function __construct(
        string $id,
        string $family,
        string $ean,
        string $name,
        string $slug,
        string $description,
        ? string $longDescription,
        float $price,
        ? float $reducedPrice,
        string $currency,
        ? int $stock = null,
        ? Manufacturer $manufacturer = null,
        ? Brand $brand = null,
        ? string $image = null,
        ? float $rating = null,
        ? DateTime $updatedAt = null,
        ? Coordinate $coordinate = null
    ) {
        $this->id = $id;
        $this->family = $family;
        $this->ean = $ean;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->longDescription = $longDescription;
        $this->price = $price;
        $this->reducedPrice = $reducedPrice;
        $this->currency = $currency;
        $this->stock = $stock;
        $this->manufacturer = $manufacturer;
        $this->brand = $brand;
        $this->categories = [];
        $this->tags = [];
        $this->image = $image;
        $this->rating = $rating;
        $this->updatedAt = $updatedAt;
        $this->coordinate = $coordinate;

        $this->recalculateRelativeValues();
    }

    /**
     * Recalculate relative values from current local parameters.
     *
     * This method should not have external class effects
     */
    public function recalculateRelativeValues()
    {
        $this->longDescription = ($this->longDescription ?? '');
        $this->image = ($this->image ?? '');
        $this->rating = !is_null($this->rating)
            ? round($this->rating, 1)
            : null;

        $this->updatedAt = ($this->updatedAt ?? new DateTime());

        $this->firstLevelSearchableData = $this->name;
        $this->secondLevelSearchableData = "$this->description $this->longDescription";

        if ($this->manufacturer instanceof Manufacturer) {
            $this->firstLevelSearchableData .= " {$this->manufacturer->getName()}";
        }

        if ($this->brand instanceof Brand) {
            $this->firstLevelSearchableData .= " {$this->brand->getName()}";
        }

        foreach ($this->tags as $tag) {
            $this->firstLevelSearchableData .= " {$tag->getName()}";
        }

        foreach ($this->categories as $category) {
            $this->firstLevelSearchableData .= " {$category->getName()}";
        }
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
     * Set name.
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        $this->recalculateRelativeValues();
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        $this->recalculateRelativeValues();
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
     * Set long description.
     *
     * @param null|string $longDescription
     */
    public function setLongDescription( ? string $longDescription)
    {
        $this->longDescription = $longDescription;
        $this->recalculateRelativeValues();
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
     * Set price.
     *
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
        $this->recalculateRelativeValues();
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
     * Set reduced price.
     *
     * @param null|float $reducedPrice
     */
    public function setReducedPrice( ? float $reducedPrice)
    {
        $this->reducedPrice = $reducedPrice;
        $this->recalculateRelativeValues();
    }

    /**
     * Get reduced price.
     *
     * @return null|float
     */
    public function getReducedPrice() : ? float
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
            $this->reducedPrice ?? $this->price
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
     * Set currency.
     *
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        $this->recalculateRelativeValues();
    }

    /**
     * Currency.
     *
     * @return mixed
     */
    public function getCurrency() : string
    {
        return $this->currency;
    }

    /**
     * Set stock.
     *
     * @param null|int $stock
     */
    public function setStock( ? int $stock)
    {
        $this->stock = $stock;
        $this->recalculateRelativeValues();
    }

    /**
     * Get stock.
     *
     * @return null|int
     */
    public function getStock() : ? int
    {
        return $this->stock;
    }

    /**
     * Set manufacturer.
     *
     * @param null|Manufacturer $manufacturer
     */
    public function setManufacturer( ? Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
        $this->recalculateRelativeValues();
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
     * Set brand.
     *
     * @param null|Brand $brand
     */
    public function setBrand( ? Brand $brand)
    {
        $this->brand = $brand;
        $this->recalculateRelativeValues();
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
        if (isset($this->categories[$category->getId()])) {
            return;
        }

        $this->categories[$category->getId()] = $category;
        $this->recalculateRelativeValues();
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
     * Remove categories.
     */
    public function removeCategories()
    {
        $this->categories = [];
        $this->recalculateRelativeValues();
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
        $this->recalculateRelativeValues();
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
     * Remove tags.
     */
    public function removeTags()
    {
        $this->tags = [];
        $this->recalculateRelativeValues();
    }

    /**
     * Set image.
     *
     * @param null|string $image
     */
    public function setImage( ? string $image)
    {
        $this->image = $image;
        $this->recalculateRelativeValues();
    }

    /**
     * Get image.
     *
     * @return null|string
     */
    public function getImage() : ? string
    {
        return $this->image;
    }

    /**
     * Set rating.
     *
     * @param null|float $rating
     */
    public function setRating( ? float $rating)
    {
        $this->rating = $rating;
        $this->recalculateRelativeValues();
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
     * Set updated at.
     *
     * @param null|DateTime $updatedAt
     */
    public function setUpdatedAt( ? DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        $this->recalculateRelativeValues();
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
     * Set coordinate.
     *
     * @param null|Coordinate $coordinate
     */
    public function setCoordinate( ? Coordinate $coordinate)
    {
        $this->coordinate = $coordinate;
        $this->recalculateRelativeValues();
    }

    /**
     * @return null|Coordinate
     */
    public function getCoordinate() : ? Coordinate
    {
        return $this->coordinate;
    }

    /**
     * Get distance.
     *
     * @return float
     */
    public function getDistance() : float
    {
        return $this->distance;
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
    public function getSecondLevelSearchableData() : string
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
            'slug' => $this->slug,
            'description' => $this->description,
            'long_description' => $this->longDescription,
            'price' => $this->price,
            'reduced_price' => $this->reducedPrice,
            'currency' => $this->currency,
            'stock' => $this->stock,
            'image' => $this->image,
            'rating' => $this->rating,
            'updated_at' => is_null($this->updatedAt)
                ? null
                : $this->updatedAt->format(DATE_ATOM),
            'coordinate' => is_null($this->coordinate)
                ? null
                : $this->coordinate->toArray(),
            'distance' => $this->distance,
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
            (string) $array['slug'],
            (string) $array['description'],
            isset($array['long_description'])
                ? ((string) $array['long_description'])
                : null,
            (float) $array['price'],
            isset($array['reduced_price'])
                ? ((float) $array['reduced_price'])
                : null,
            (string) $array['currency'],
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
                : null,
            isset($array['coordinate'])
                ? Coordinate::createFromArray($array['coordinate'])
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

        if (isset($array['distance'])) {
            $product->distance = (float) $array['distance'];
        }

        return $product;
    }
}
