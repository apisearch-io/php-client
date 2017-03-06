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

namespace Puntmig\Search\Query;

use Puntmig\Search\Result\Result;

/**
 * Class UrlBuilder.
 */
class UrlBuilder
{
    /**
     * Add filter into query.
     *
     * @param Query  $query
     * @param string $filterName
     * @param string $value
     *
     * @return array
     */
    public function addFilterValue(
        Query $query,
        string $filterName,
        string $value
    ) : array {
        $urlParameters = $this->generateQueryUrlParameters($query);

        /**
         * Silent pass if the filter is already applied.
         */
        if (
            isset($urlParameters[$filterName]) &&
            in_array($value, $urlParameters[$filterName])
        ) {
            return $urlParameters;
        }

        $urlParameters[$filterName][] = $value;

        return $urlParameters;
    }

    /**
     * Remove filter from query.
     *
     * @param Query  $query
     * @param string $filterName
     * @param string $value
     *
     * @return array
     */
    public function removeFilterValue(
        Query $query,
        string $filterName,
        string $value = null
    ) : array {
        $urlParameters = $this->generateQueryUrlParameters($query);
        if (is_null($value)) {
            unset($urlParameters[$filterName]);

            return $urlParameters;
        }

        /**
         * Silent pass if the filter does not exist.
         */
        if (!isset($urlParameters[$filterName])) {
            return $urlParameters;
        }

        if (($key = array_search($value, $urlParameters[$filterName])) !== false) {
            unset($urlParameters[$filterName][$key]);
        }

        return $urlParameters;
    }

    /**
     * Change price range.
     *
     * @param Query $query
     *
     * @return array
     */
    public function removePriceRangeFilter(Query $query) : array
    {
        $urlParameters = $this->generateQueryUrlParameters($query);
        unset($urlParameters['price']);

        return $urlParameters;
    }

    /**
     * Set pagination.
     *
     * @param Query $query
     * @param int   $page
     *
     * @return array
     */
    public function addPage(
        Query $query,
        int $page
    ) : array {
        $urlParameters = $this->generateQueryUrlParameters($query);
        $urlParameters['page'] = $page;

        return $urlParameters;
    }

    /**
     * Add previous page.
     *
     * @param Query $query
     *
     * @return null|array
     */
    public function addPrevPage(Query $query) : ? array
    {
        $urlParameters = $this->generateQueryUrlParameters($query);
        $page = $query->getPage();
        $prevPage = $page - 1;

        if ($prevPage < 1) {
            return null;
        }

        $urlParameters['page'] = $prevPage;

        return $urlParameters;
    }

    /**
     * Add next page.
     *
     * @param Query  $query
     * @param Result $result
     *
     * @return null|array
     */
    public function addNextPage(Query $query, Result $result) : ? array
    {
        $urlParameters = $this->generateQueryUrlParameters($query);
        $page = $query->getPage();
        $nextPage = $page + 1;

        if ((($nextPage - 1) * $query->getSize()) > $result->getTotalHits()) {
            return null;
        }

        $urlParameters['page'] = $nextPage;

        return $urlParameters;
    }

    /**
     * Add sort by. Return null if doesn't change.
     *
     * @param Query  $query
     * @param string $field
     * @param string $mode
     *
     * @return null|array
     */
    public function addSortBy(
        Query $query,
        string $field,
        string $mode
    ) : ? array {
        $urlParameters = $this->generateQueryUrlParameters($query);

        if (
            isset($urlParameters['sort_by'][$field]) &&
            $urlParameters['sort_by'][$field] == $mode
        ) {
            return null;
        }

        if (
            !isset($urlParameters['sort_by']) &&
            SortBy::SCORE === [$field => $mode]
        ) {
            return null;
        }

        unset($urlParameters['sort_by']);

        if (SortBy::SCORE !== [$field => $mode]) {
            $urlParameters['sort_by'][$field] = $mode;
        }

        return $urlParameters;
    }

    /**
     * Query to url parameters.
     *
     * @param Query $query
     *
     * @return array
     */
    private function generateQueryUrlParameters(Query $query) : array
    {
        $parameters = array_filter(
            array_map(function (Filter $filter) {
                return $filter->getValues();
            }, $query->getFilters())
        );
        unset($parameters['_query']);

        $queryString = $query
            ->getFilter('_query')
            ->getValues()[0];

        if (!empty($queryString)) {
            $parameters['q'] = $queryString;
        }

        $sort = $query->getSortBy();
        if ($sort !== SortBy::SCORE) {
            $parameters['sort_by'] = $sort;
        }

        return $parameters;
    }
}
