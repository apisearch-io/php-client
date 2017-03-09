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
     * Elements
     */
    private $elementsBulk;

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
        $this->resetElementsBulk();
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
    private function resetElementsBulk()
    {
        $this->elementsBulk = [
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
        $this->elementsBulk['products'][] = $product;

        if ($product->getManufacturer() instanceof Manufacturer) {
            $this->addManufacturer(
                $product->getManufacturer()
            );
        }

        if ($product->getBrand() instanceof Brand) {
            $this->addBrand(
                $product->getBrand()
            );
        }

        foreach ($product->getCategories() as $category) {
            $this->addCategory($category);
        }

        foreach ($product->getTags() as $tag) {
            $this->addTag($tag);
        }
    }

    /**
     * Add Category.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->elementsBulk['categories'][$category->getId()] = $category;
    }

    /**
     * Index manufacturer.
     *
     * @param Manufacturer $manufacturer
     */
    public function addManufacturer(Manufacturer $manufacturer)
    {
        $manufacturerId = $manufacturer->getId();
        if (isset($this->elementsBulk['manufacturers'][$manufacturerId])) {
            return;
        }

        $this->elementsBulk['manufacturers'][$manufacturerId] = $manufacturer;
    }

    /**
     * Index brand.
     *
     * @param Brand $brand
     */
    public function addBrand(Brand $brand)
    {
        $brandId = $brand->getId();
        if (isset($this->elementsBulk['brands'][$brandId])) {
            return;
        }

        $this->elementsBulk['brands'][$brandId] = $brand;
    }

    /**
     * Index tag.
     *
     * @param Tag $tag
     */
    private function addTag(Tag $tag)
    {
        $tagId = $tag->getName();
        if (isset($this->elementsBulk['tags'][$tagId])) {
            return;
        }

        $this->elementsBulk['tags'][$tagId] = $tag;
    }

    /**
     * Flush.
     *
     * @param int $bulkNumber
     */
    public function flush(int $bulkNumber = 500)
    {
        $offset = 0;
        while (true) {
            $products = array_slice(
                $this->elementsBulk['products'],
                $offset,
                $bulkNumber
            );

            if (empty($products)) {
                break;
            }

            $this->flushProducts($products);
            $this->flushCategories($this->elementsBulk['categories']);
            $this->flushManufacturers($this->elementsBulk['manufacturers']);
            $this->flushBrands($this->elementsBulk['brands']);
            $this->flushTags($this->elementsBulk['tags']);
            $offset += $bulkNumber;
        }

        $this->resetElementsBulk();
    }

    /**
     * Flush products.
     *
     * @param Product[] $products
     */
    abstract protected function flushProducts(array $products);

    /**
     * Flush categories.
     *
     * @param Category[] $categories
     */
    abstract protected function flushCategories(array $categories);

    /**
     * Flush manufacturers.
     *
     * @param Manufacturer[] $manufacturers
     */
    abstract protected function flushManufacturers(array $manufacturers);

    /**
     * Flush brands.
     *
     * @param Brand[] $brands
     */
    abstract protected function flushBrands(array $brands);

    /**
     * Flush tags.
     *
     * @param Tag[] $tags
     */
    abstract protected function flushTags(array $tags);

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
