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
        $productUUID = $product
            ->getProductReference()
            ->composeUUID();

        $this->elementsToUpdate['products'][$productUUID] = $product;
        unset($this->elementsToDelete['products'][$productUUID]);
    }

    /**
     * Delete product document by id.
     *
     * @param ProductReference $productReference
     */
    public function deleteProduct(ProductReference $productReference)
    {
        $productUUID = $productReference->composeUUID();
        $this->elementsToDelete['products'][$productUUID] = $productReference;
        unset($this->elementsToUpdate['products'][$productUUID]);
    }

    /**
     * Add Category.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $categoryUUID = $category
            ->getCategoryReference()
            ->composeUUID();

        $this->elementsToUpdate['categories'][$categoryUUID] = $category;
        unset($this->elementsToDelete['categories'][$categoryUUID]);
    }

    /**
     * Delete category.
     *
     * @param CategoryReference $categoryReference
     */
    public function deleteCategory(CategoryReference $categoryReference)
    {
        $categoryUUID = $categoryReference->composeUUID();
        $this->elementsToDelete['categories'][$categoryUUID] = $categoryReference;
        unset($this->elementsToUpdate['categories'][$categoryUUID]);
    }

    /**
     * Index manufacturer.
     *
     * @param Manufacturer $manufacturer
     */
    public function addManufacturer(Manufacturer $manufacturer)
    {
        $manufacturerUUID = $manufacturer
            ->getManufacturerReference()
            ->composeUUID();

        $this->elementsToUpdate['manufacturers'][$manufacturerUUID] = $manufacturer;
        unset($this->elementsToDelete['manufacturers'][$manufacturerUUID]);
    }

    /**
     * Delete manufacturer.
     *
     * @param ManufacturerReference $manufacturerReference
     */
    public function deleteManufacturer(ManufacturerReference $manufacturerReference)
    {
        $manufacturerUUID = $manufacturerReference->composeUUID();
        $this->elementsToDelete['manufacturers'][$manufacturerUUID] = $manufacturerReference;
        unset($this->elementsToUpdate['manufacturers'][$manufacturerUUID]);
    }

    /**
     * Index brand.
     *
     * @param Brand $brand
     */
    public function addBrand(Brand $brand)
    {
        $brandUUID = $brand
            ->getBrandReference()
            ->composeUUID();

        $this->elementsToUpdate['brands'][$brandUUID] = $brand;
        unset($this->elementsToDelete['brands'][$brandUUID]);
    }

    /**
     * Delete brand.
     *
     * @param BrandReference $brandReference
     */
    public function deleteBrand(BrandReference $brandReference)
    {
        $brandUUID = $brandReference->composeUUID();
        $this->elementsToDelete['brands'][$brandUUID] = $brandReference;
        unset($this->elementsToUpdate['brands'][$brandUUID]);
    }

    /**
     * Index tag.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $tagUUID = $tag
            ->getTagReference()
            ->composeUUID();

        $this->elementsToUpdate['tags'][$tagUUID] = $tag;
        unset($this->elementsToDelete['tags'][$tagUUID]);
    }

    /**
     * Delete tag.
     *
     * @param TagReference $tagReference
     */
    public function deleteTag(TagReference $tagReference)
    {
        $tagUUID = $tagReference->composeUUID();
        $this->elementsToDelete['tags'][$tagUUID] = $tagReference;
        unset($this->elementsToUpdate['tags'][$tagUUID]);
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
     * @param Product[]          $productsToUpdate
     * @param ProductReference[] $productsToDelete
     */
    abstract protected function flushProducts(
        array $productsToUpdate,
        array $productsToDelete
    );

    /**
     * Flush categories.
     *
     * @param Category[]          $categoriesToUpdate
     * @param CategoryReference[] $categoriesToDelete
     */
    abstract protected function flushCategories(
        array $categoriesToUpdate,
        array $categoriesToDelete
    );

    /**
     * Flush manufacturers.
     *
     * @param Manufacturer[]          $manufacturersToUpdate
     * @param ManufacturerReference[] $manufacturersToDelete
     */
    abstract protected function flushManufacturers(
        array $manufacturersToUpdate,
        array $manufacturersToDelete
    );

    /**
     * Flush brands.
     *
     * @param Brand[]          $brandsToUpdate
     * @param BrandReference[] $brandsToDelete
     */
    abstract protected function flushBrands(
        array $brandsToUpdate,
        array $brandsToDelete
    );

    /**
     * Flush tags.
     *
     * @param Tag[]          $tagsToUpdate
     * @param TagReference[] $tagsToDelete
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
