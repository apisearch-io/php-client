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
use Puntmig\Search\Model\BrandReference;
use Puntmig\Search\Model\Category;
use Puntmig\Search\Model\CategoryReference;
use Puntmig\Search\Model\Manufacturer;
use Puntmig\Search\Model\ManufacturerReference;
use Puntmig\Search\Model\Product;
use Puntmig\Search\Model\ProductReference;
use Puntmig\Search\Model\Tag;
use Puntmig\Search\Model\TagReference;
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
     * @param Product[]          $productsToUpdate
     * @param ProductReference[] $productsToDelete
     */
    protected function flushProducts(
        array $productsToUpdate,
        array $productsToDelete
    ) {
        if (!empty($productsToUpdate)) {
            $this
                ->httpClient
                ->get('/products', 'post', [
                    'key' => $this->getKey(),
                    'products' => json_encode(
                        array_map(function (Product $product) {
                            return $product->toArray();
                        }, $productsToUpdate)
                    ),
                ]);
        }

        if (!empty($productsToDelete)) {
            $this
                ->httpClient
                ->get('/products', 'delete', [
                    'key' => $this->getKey(),
                    'products' => json_encode(
                        array_map(function (ProductReference $product) {
                            return $product->toArray();
                        }, $productsToDelete)
                    ),
                ]);
        }
    }

    /**
     * Flush categories.
     *
     * @param Category[]          $categoriesToUpdate
     * @param CategoryReference[] $categoriesToDelete
     */
    protected function flushCategories(
        array $categoriesToUpdate,
        array $categoriesToDelete
    ) {
        if (!empty($categoriesToUpdate)) {
            $this
                ->httpClient
                ->get('/categories', 'post', [
                    'key' => $this->getKey(),
                    'categories' => json_encode(
                        array_map(function (Category $category) {
                            return $category->toArray();
                        }, $categoriesToUpdate)
                    ),
                ]);
        }

        if (!empty($categoriesToDelete)) {
            $this
                ->httpClient
                ->get('/categories', 'delete', [
                    'key' => $this->getKey(),
                    'categories' => json_encode(
                        array_map(function (CategoryReference $category) {
                            return $category->toArray();
                        }, $categoriesToDelete)
                    ),
                ]);
        }
    }

    /**
     * Flush manufacturers.
     *
     * @param Manufacturer[]          $manufacturersToUpdate
     * @param ManufacturerReference[] $manufacturersToDelete
     */
    protected function flushManufacturers(
        array $manufacturersToUpdate,
        array $manufacturersToDelete
    ) {
        if (!empty($manufacturersToUpdate)) {
            $this
                ->httpClient
                ->get('/manufacturers', 'post', [
                    'key' => $this->getKey(),
                    'manufacturers' => json_encode(
                        array_map(function (Manufacturer $manufacturer) {
                            return $manufacturer->toArray();
                        }, $manufacturersToUpdate)
                    ),
                ]);
        }

        if (!empty($manufacturersToDelete)) {
            $this
                ->httpClient
                ->get('/manufacturers', 'delete', [
                    'key' => $this->getKey(),
                    'manufacturers' => json_encode(
                        array_map(function (ManufacturerReference $manufacturer) {
                            return $manufacturer->toArray();
                        }, $manufacturersToDelete)
                    ),
                ]);
        }
    }

    /**
     * Flush brands.
     *
     * @param Brand[]          $brandsToUpdate
     * @param BrandReference[] $brandsToDelete
     */
    protected function flushBrands(
        array $brandsToUpdate,
        array $brandsToDelete
    ) {
        if (!empty($brandsToUpdate)) {
            $this
                ->httpClient
                ->get('/brands', 'post', [
                    'key' => $this->getKey(),
                    'brands' => json_encode(
                        array_map(function (Brand $brand) {
                            return $brand->toArray();
                        }, $brandsToUpdate)
                    ),
                ]);
        }

        if (!empty($brandsToDelete)) {
            $this
                ->httpClient
                ->get('/brands', 'delete', [
                    'key' => $this->getKey(),
                    'brands' => json_encode(
                        array_map(function (BrandReference $brand) {
                            return $brand->toArray();
                        }, $brandsToDelete)
                    ),
                ]);
        }
    }

    /**
     * Flush tags.
     *
     * @param Tag[]          $tagsToUpdate
     * @param TagReference[] $tagsToDelete
     */
    protected function flushTags(
        array $tagsToUpdate,
        array $tagsToDelete
    ) {
        if (!empty($tagsToUpdate)) {
            $this
                ->httpClient
                ->get('/tags', 'post', [
                    'key' => $this->getKey(),
                    'tags' => json_encode(
                        array_map(function (Tag $tag) {
                            return $tag->toArray();
                        }, $tagsToUpdate)
                    ),
                ]);
        }

        if (!empty($tagsToDelete)) {
            $this
                ->httpClient
                ->get('/tags', 'delete', [
                    'key' => $this->getKey(),
                    'tags' => json_encode(
                        array_map(function (TagReference $tag) {
                            return $tag->toArray();
                        }, $tagsToDelete)
                    ),
                ]);
        }
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
                'query' => json_encode($query->toArray()),
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
