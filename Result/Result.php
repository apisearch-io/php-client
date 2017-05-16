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

namespace Puntmig\Search\Result;

use Puntmig\Search\Model\Brand;
use Puntmig\Search\Model\Category;
use Puntmig\Search\Model\HttpTransportable;
use Puntmig\Search\Model\Manufacturer;
use Puntmig\Search\Model\Product;
use Puntmig\Search\Model\Tag;
use Puntmig\Search\Query\Query;

/**
 * Class Result.
 */
class Result implements HttpTransportable
{
    /**
     * @var array
     *
     * Abbreviations
     */
    private $abbreviations = [
        'p' => 'products',
        'c' => 'categories',
        'm' => 'manufacturers',
        'b' => 'brands',
        't' => 'tags',
    ];

    /**
     * @var Query
     *
     * Query associated
     */
    private $query;

    /**
     * @var Product[]
     *
     * Products
     */
    private $products = [];

    /**
     * @var Category[]
     *
     * Categories
     */
    private $categories = [];

    /**
     * @var Manufacturer[]
     *
     * Manufacturers
     */
    private $manufacturers = [];

    /**
     * @var Brand[]
     *
     * Brands
     */
    private $brands = [];

    /**
     * @var Tag[]
     *
     * Tags
     */
    private $tags = [];

    /**
     * @var array
     *
     * Results
     */
    private $results = [];

    /**
     * @var array
     *
     * Suggests
     */
    private $suggests = [];

    /**
     * @var null|Aggregations
     *
     * Aggregations
     */
    private $aggregations;

    /**
     * Total elements.
     *
     * @var int
     */
    private $totalElements;

    /**
     * Total products.
     *
     * @var int
     */
    private $totalProducts;

    /**
     * Total hits.
     *
     * @var int
     */
    private $totalHits;

    /**
     * Min price.
     *
     * @var int
     */
    private $minPrice;

    /**
     * Max price.
     *
     * @var int
     */
    private $maxPrice;

    /**
     * @var float
     *
     * Price average
     */
    private $priceAverage;

    /**
     * @var float
     *
     * Rating average
     */
    private $ratingAverage;

    /**
     * Result constructor.
     *
     * @param Query $query
     * @param int   $totalElements
     * @param int   $totalProducts
     * @param int   $totalHits
     * @param int   $minPrice
     * @param int   $maxPrice
     * @param float $priceAverage
     * @param float $ratingAverage
     */
    public function __construct(
        Query $query,
        int $totalElements,
        int $totalProducts,
        int $totalHits,
        int $minPrice,
        int $maxPrice,
        float $priceAverage,
        float $ratingAverage
    ) {
        $this->query = $query;
        $this->totalElements = $totalElements;
        $this->totalProducts = $totalProducts;
        $this->totalHits = $totalHits;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
        $this->priceAverage = $priceAverage;
        $this->ratingAverage = $ratingAverage;
    }

    /**
     * Add product.
     *
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        $productUUID = $product
            ->getProductReference()
            ->composeUUID();

        $this->products[$productUUID] = $product;
        $this->results[] = ['p', $productUUID];
    }

    /**
     * Get products.
     *
     * @return Product[]
     */
    public function getProducts(): array
    {
        return array_values($this->products);
    }

    /**
     * Add category.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $categoryUUID = $category
            ->getCategoryReference()
            ->composeUUID();

        $this->categories[$categoryUUID] = $category;
        $this->results[] = ['c', $categoryUUID];
    }

    /**
     * Get categories.
     *
     * @return Category[]
     */
    public function getCategories(): array
    {
        return array_values($this->categories);
    }

    /**
     * Add manufacturer.
     *
     * @param Manufacturer $manufacturer
     */
    public function addManufacturer(Manufacturer $manufacturer)
    {
        $manufacturerUUID = $manufacturer
            ->getManufacturerReference()
            ->composeUUID();

        $this->manufacturers[$manufacturerUUID] = $manufacturer;
        $this->results[] = ['m', $manufacturerUUID];
    }

    /**
     * Get manufacturers.
     *
     * @return Manufacturer[]
     */
    public function getManufacturers(): array
    {
        return array_values($this->manufacturers);
    }

    /**
     * Add brand.
     *
     * @param Brand $brand
     */
    public function addBrand(Brand $brand)
    {
        $brandUUID = $brand
            ->getBrandReference()
            ->composeUUID();

        $this->brands[$brandUUID] = $brand;
        $this->results[] = ['b', $brandUUID];
    }

    /**
     * Get brands.
     *
     * @return Brand[]
     */
    public function getBrands(): array
    {
        return array_values($this->brands);
    }

    /**
     * Add tag.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $tagUUID = $tag
            ->getTagReference()
            ->composeUUID();

        $this->tags[$tagUUID] = $tag;
        $this->results[] = ['t', $tagUUID];
    }

    /**
     * Get tags.
     *
     * @return Tag[]
     */
    public function getTags(): array
    {
        return array_values($this->tags);
    }

    /**
     * Get results.
     *
     * @return array
     */
    public function getResults() : array
    {
        return array_values(
            array_map(function (array $result) {
                $container = $this->abbreviations[$result[0]];

                return $this->$container[$result[1]];
            }, $this->results)
        );
    }

    /**
     * Get first result.
     *
     * @return null|mixed
     */
    public function getFirstResult()
    {
        $results = $this->getResults();

        if (empty($results)) {
            return null;
        }

        $firstResult = reset($results);

        return $firstResult;
    }

    /**
     * Set aggregations.
     *
     * @param Aggregations $aggregations
     */
    public function setAggregations(Aggregations $aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get aggregations.
     *
     * @return null|Aggregations
     */
    public function getAggregations(): ? Aggregations
    {
        return $this->aggregations;
    }

    /**
     * Get aggregation.
     *
     * @param string $name
     *
     * @return null|Aggregation
     */
    public function getAggregation(string $name) : ? Aggregation
    {
        if (is_null($this->aggregations)) {
            return null;
        }

        return $this
            ->aggregations
            ->getAggregation($name);
    }

    /**
     * Has not empty aggregation.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasNotEmptyAggregation(string $name) : bool
    {
        if (is_null($this->aggregations)) {
            return false;
        }

        return $this
            ->aggregations
            ->hasNotEmptyAggregation($name);
    }

    /**
     * Get metadata aggregation.
     *
     * @param string $field
     *
     * @return null|Aggregation
     */
    public function getMetaAggregation(string $field) : ? Aggregation
    {
        if (is_null($this->aggregations)) {
            return null;
        }

        return $this
            ->aggregations
            ->getMetaAggregation($field);
    }

    /**
     * Add suggest.
     *
     * @param string $suggest
     */
    public function addSuggest(string $suggest)
    {
        $this->suggests[$suggest] = $suggest;
    }

    /**
     * Get suggests.
     *
     * @return string[]
     */
    public function getSuggests() : array
    {
        return array_values($this->suggests);
    }

    /**
     * Get query.
     *
     * @return Query
     */
    public function getQuery() : Query
    {
        return $this->query;
    }

    /**
     * Total elements.
     *
     * @return int
     */
    public function getTotalElements() : int
    {
        return $this->totalElements;
    }

    /**
     * Total products.
     *
     * @return int
     */
    public function getTotalProducts(): int
    {
        return $this->totalProducts;
    }

    /**
     * Get total hits.
     *
     * @return int
     */
    public function getTotalHits() : int
    {
        return $this->totalHits;
    }

    /**
     * Get min price.
     *
     * @return int
     */
    public function getMinPrice(): int
    {
        return $this->minPrice;
    }

    /**
     * Get max price.
     *
     * @return int
     */
    public function getMaxPrice(): int
    {
        return $this->maxPrice;
    }

    /**
     * Get price average.
     *
     * @return float
     */
    public function getPriceAverage(): float
    {
        return $this->priceAverage;
    }

    /**
     * Get rating average.
     *
     * @return float
     */
    public function getRatingAverage(): float
    {
        return $this->ratingAverage;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return array_filter([
            'query' => $this->query->toArray(),
            'total_elements' => $this->totalElements,
            'total_products' => $this->totalProducts,
            'total_hits' => $this->totalHits,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'price_average' => $this->priceAverage,
            'rating_average' => $this->ratingAverage,
            'products' => array_map(function (Product $product) {
                return $product->toArray();
            }, $this->products),
            'categories' => array_map(function (Category $category) {
                return $category->toArray();
            }, $this->categories),
            'brands' => array_map(function (Brand $brand) {
                return $brand->toArray();
            }, $this->brands),
            'manufacturers' => array_map(function (Manufacturer $manufacturer) {
                return $manufacturer->toArray();
            }, $this->manufacturers),
            'tags' => array_map(function (Tag $tag) {
                return $tag->toArray();
            }, $this->tags),
            'results' => $this->results,
            'aggregations' => $this->aggregations instanceof Aggregations
                ? $this
                    ->aggregations
                    ->toArray()
                : null,
            'suggests' => $this->suggests,
        ]);
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
        $result = new self(
            Query::createFromArray($array['query']),
            $array['total_elements'] ?? 0,
            $array['total_products'] ?? 0,
            $array['total_hits'] ?? 0,
            $array['min_price'] ?? 0,
            $array['max_price'] ?? 0,
            $array['price_average'] ?? 0,
            $array['rating_average'] ?? 0
        );

        $result->products = array_map(function (array $product) {
            return Product::createFromArray($product);
        }, $array['products'] ?? []);

        $result->categories = array_map(function (array $category) {
            return Category::createFromArray($category);
        }, $array['categories'] ?? []);

        $result->manufacturers = array_map(function (array $manufacturer) {
            return Manufacturer::createFromArray($manufacturer);
        }, $array['manufacturers'] ?? []);

        $result->brands = array_map(function (array $brand) {
            return Brand::createFromArray($brand);
        }, $array['brands'] ?? []);

        $result->tags = array_map(function (array $tag) {
            return Tag::createFromArray($tag);
        }, $array['tags'] ?? []);

        $result->results = $array['results'] ?? [];

        if (isset($array['aggregations'])) {
            $result->aggregations = Aggregations::createFromArray($array['aggregations']);
        }

        $result->suggests = $array['suggests'] ?? [];

        return $result;
    }
}
