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

use Puntmig\Search\Model\Brand;
use Puntmig\Search\Model\Category;
use Puntmig\Search\Model\Manufacturer;
use Puntmig\Search\Model\Product;
use Puntmig\Search\Model\Tag;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Result\Result;

/**
 * Abstract class Repository.
 */
abstract class Repository
{
    /**
     * @var array
     *
     * Elements to update
     */
    private $elementsToUpdate;

    /**
     * @var array
     *
     * Elements to delete
     */
    private $elementsToDelete;

    /**
     * @var string
     *
     * Api key
     */
    private $key;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->resetCachedElements();
    }

    /**
     * Set key.
     *
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Reset cache.
     */
    private function resetCachedElements()
    {
        $this->elementsToUpdate = [
            'products' => [],
            'categories' => [],
            'manufacturers' => [],
            'brands' => [],
            'tags' => [],
        ];

        $this->elementsToDelete = [
            'products' => [],
            'categories' => [],
            'manufacturers' => [],
            'brands' => [],
            'tags' => [],
        ];
    }

    /**
     * Generate product document.
     *
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        $productId = $product->getId();
        if (isset($this->elementsToUpdate['products'][$productId])) {
            return;
        }

        $this->elementsToUpdate['products'][$productId] = $product;
    }

    /**
     * Remove product document by id.
     *
     * @param string $productId
     */
    public function removeProduct(string $productId)
    {
        unset($this->elementsToUpdate['products'][$productId]);
        $this->elementsToDelete['products'][] = $productId;
    }

    /**
     * Add Category.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $categoryId = $category->getId();
        if (isset($this->elementsToUpdate['categories'][$categoryId])) {
            return;
        }

        $this->elementsToUpdate['categories'][$categoryId] = $category;
    }

    /**
     * Remove category.
     *
     * @param string $categoryId
     */
    public function removeCategory(string $categoryId)
    {
        unset($this->elementsToUpdate['categories'][$categoryId]);
        $this->elementsToDelete['categories'][] = $categoryId;
    }

    /**
     * Index manufacturer.
     *
     * @param Manufacturer $manufacturer
     */
    public function addManufacturer(Manufacturer $manufacturer)
    {
        $manufacturerId = $manufacturer->getId();
        if (isset($this->elementsToUpdate['manufacturers'][$manufacturerId])) {
            return;
        }

        $this->elementsToUpdate['manufacturers'][$manufacturerId] = $manufacturer;
    }

    /**
     * Remove manufacturer document by id.
     *
     * @param string $manufacturerId
     */
    public function removeManufacturer(string $manufacturerId)
    {
        unset($this->elementsToUpdate['manufacturers'][$manufacturerId]);
        $this->elementsToDelete['manufacturers'][] = $manufacturerId;
    }

    /**
     * Index brand.
     *
     * @param Brand $brand
     */
    public function addBrand(Brand $brand)
    {
        $brandId = $brand->getId();
        if (isset($this->elementsToUpdate['brands'][$brandId])) {
            return;
        }

        $this->elementsToUpdate['brands'][$brandId] = $brand;
    }

    /**
     * Remove brand document by id.
     *
     * @param string $brandId
     */
    public function removeBrand(string $brandId)
    {
        unset($this->elementsToUpdate['brands'][$brandId]);
        $this->elementsToDelete['brands'][] = $brandId;
    }

    /**
     * Index tag.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $tagId = $tag->getName();
        if (isset($this->elementsToUpdate['tags'][$tagId])) {
            return;
        }

        $this->elementsToUpdate['tags'][$tagId] = $tag;
    }

    /**
     * Remove tag document by id.
     *
     * @param string $tagId
     */
    public function removeTag(string $tagId)
    {
        unset($this->elementsToUpdate['tags'][$tagId]);
        $this->elementsToDelete['tags'][] = $tagId;
    }

    /**
     * Flush all.
     *
     * This flush can be avoided if not enough products have been generated by
     * setting $skipIfLess = true
     *
     * @param int  $bulkNumber
     * @param bool $skipIfLess
     */
    public function flush(
        int $bulkNumber = 500,
        bool $skipIfLess = false
    ) {
        if (
            $skipIfLess &&
            count($this->elementsToUpdate['products']) < $bulkNumber
        ) {
            return;
        }

        $offset = 0;
        while (true) {
            $products = array_slice(
                $this->elementsToUpdate['products'],
                $offset,
                $bulkNumber
            );

            if (empty($products)) {
                break;
            }

            $this->flushProducts($products, []);
            $offset += $bulkNumber;
        }

        $this->flushProducts([], $this->elementsToDelete['products']);

        $this->flushCategories(
            $this->elementsToUpdate['categories'],
            $this->elementsToDelete['categories']
        );

        $this->flushManufacturers(
            $this->elementsToUpdate['manufacturers'],
            $this->elementsToDelete['manufacturers']
        );

        $this->flushBrands(
            $this->elementsToUpdate['brands'],
            $this->elementsToDelete['brands']
        );

        $this->flushTags(
            $this->elementsToUpdate['tags'],
            $this->elementsToDelete['tags']
        );

        $this->resetCachedElements();
    }

    /**
     * Flush products.
     *
     * @param Product[] $productsToUpdate
     * @param string[]  $productsToDelete
     */
    abstract protected function flushProducts(
        array $productsToUpdate,
        array $productsToDelete
    );

    /**
     * Flush categories.
     *
     * @param Category[] $categoriesToUpdate
     * @param string[]   $categoriesToDelete
     */
    abstract protected function flushCategories(
        array $categoriesToUpdate,
        array $categoriesToDelete
    );

    /**
     * Flush manufacturers.
     *
     * @param Manufacturer[] $manufacturersToUpdate
     * @param string[]       $manufacturersToDelete
     */
    abstract protected function flushManufacturers(
        array $manufacturersToUpdate,
        array $manufacturersToDelete
    );

    /**
     * Flush brands.
     *
     * @param Brand[]  $brandsToUpdate
     * @param string[] $brandsToDelete
     */
    abstract protected function flushBrands(
        array $brandsToUpdate,
        array $brandsToDelete
    );

    /**
     * Flush tags.
     *
     * @param Tag[]    $tagsToUpdate
     * @param string[] $tagsToDelete
     */
    abstract protected function flushTags(
        array $tagsToUpdate,
        array $tagsToDelete
    );

    /**
     * Search across the index types.
     *
     * @param Query $query
     *
     * @return Result
     */
    abstract public function query(Query $query) : Result;

    /**
     * Reset the index.
     */
    abstract public function reset();
}
