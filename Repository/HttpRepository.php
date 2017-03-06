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

namespace Puntmig\Search\Repository;

use Puntmig\Search\Http\HttpClient;
use Puntmig\Search\Model\Brand;
use Puntmig\Search\Model\Category;
use Puntmig\Search\Model\Manufacturer;
use Puntmig\Search\Model\Product;
use Puntmig\Search\Model\Tag;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

/**
 * Class HttpRepository.
 */
class HttpRepository extends Repository
{
    /**
     * @var HttpClient
     *
     * Http client
     */
    private $httpClient;

    /**
     * HttpAdapter constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
    }

    /**
     * Flush products.
     *
     * @param Product[] $products
     */
    protected function flushProducts(array $products)
    {
        $this
            ->httpClient
            ->get('/index/products', 'post', [
                'key' => $this->getKey(),
                'products' => array_map(function (Product $product) {
                    return $product->toArray();
                }, $products),
            ]);
    }

    /**
     * Flush categories.
     *
     * @param Category[] $categories
     */
    protected function flushCategories(array $categories)
    {
        $this
            ->httpClient
            ->get('/index/categories', 'post', [
                'key' => $this->getKey(),
                'categories' => array_map(function (Category $category) {
                    return $category->toArray();
                }, $categories),
            ]);
    }

    /**
     * Flush manufacturers.
     *
     * @param Manufacturer[] $manufacturers
     */
    protected function flushManufacturers(array $manufacturers)
    {
        $this
            ->httpClient
            ->get('/index/manufacturers', 'post', [
                'key' => $this->getKey(),
                'manufacturers' => array_map(function (Manufacturer $manufacturer) {
                    return $manufacturer->toArray();
                }, $manufacturers),
            ]);
    }

    /**
     * Flush brands.
     *
     * @param Brand[] $brands
     */
    protected function flushBrands(array $brands)
    {
        $this
            ->httpClient
            ->get('/index/brands', 'post', [
                'key' => $this->getKey(),
                'brands' => array_map(function (Brand $brand) {
                    return $brand->toArray();
                }, $brands),
            ]);
    }

    /**
     * Flush tags.
     *
     * @param Tag[] $tags
     */
    protected function flushTags(array $tags)
    {
        $this
            ->httpClient
            ->get('/index/tags', 'post', [
                'key' => $this->getKey(),
                'tags' => array_map(function (Tag $tag) {
                    return $tag->toArray();
                }, $tags),
            ]);
    }

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
     */
    public function query(Query $query): Result
    {
        $response = $this
            ->httpClient
            ->get('/query', 'get', [
                'key' => $this->getKey(),
                'query' => $query->toArray(),
            ]);

        return Result::createFromArray($response['body']);
    }

    /**
     * Reset the index.
     */
    public function reset()
    {
        $this
            ->httpClient
            ->get('/reset', 'delete', [
                'key' => $this->getKey(),
            ]);
    }
}
