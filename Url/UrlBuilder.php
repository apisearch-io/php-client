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

namespace Puntmig\Search\Url;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

use Puntmig\Search\Query\Filter;
use Puntmig\Search\Query\SortBy;
use Puntmig\Search\Result\Result;

/**
 * Class UrlBuilder.
 */
class UrlBuilder
{
    /**
     * Routes dictionary.
     */
    private $routesDictionary = [];

    /**
     * @var RouterInterface
     *
     * Router
     */
    private $router;

    /**
     * @var array
     *
     * Routes cache
     */
    private $routesCache = [];

    /**
     * UrlBuilder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Set routes dictionary.
     *
     * @param array $routesDictionary
     */
    public function setRoutesDictionary(array $routesDictionary)
    {
        $this->routesDictionary = $routesDictionary;
    }

    /**
     * Add filter into query.
     *
     * @param Result $result
     * @param string $filterName
     * @param string $value
     *
     * @return string
     */
    public function addFilterValue(
        Result $result,
        string $filterName,
        string $value
    ) : string {
        if (isset($this->routesCache[spl_object_hash($result)][$filterName])) {
            return str_replace(
                ['{id}', '{slug}'],
                $result->getAggregation($filterName)->getCounter($value)->getValues(),
                $this->routesCache[spl_object_hash($result)][$filterName]
            );
        }

        $urlParameters = $this->generateQueryUrlParameters($result, $filterName);
        if (
            !isset($urlParameters[$filterName]) ||
            !in_array($value, $urlParameters[$filterName])
        ) {
            $urlParameters[$filterName][] = $value;
        }

        $urlElements = $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        );

        $templateRoute = in_array($urlElements['field'], [false, $filterName])
            ? $urlElements['template_path']
            : parse_url($urlElements['route'], PHP_URL_PATH);

        $filteredUrlParameters = $urlElements['url_parameters'];
        $routeQuery = parse_url($urlElements['route'], PHP_URL_QUERY);
        $route = rtrim("$templateRoute?$routeQuery", '?');
        if (isset($filteredUrlParameters[$filterName])) {
            $paremeterKey = array_search($value, $filteredUrlParameters[$filterName]);
            $route = str_replace(
                "{$filterName}[$paremeterKey]=$value",
                "{$filterName}[$paremeterKey]={id}",
                $route
            );
        }

        $this->routesCache[spl_object_hash($result)][$filterName] = $route;

        return $urlElements['route'];
    }

    /**
     * Remove filter from query.
     *
     * @param Result $result
     * @param string $filterName
     * @param string $value
     *
     * @return string
     */
    public function removeFilterValue(
        Result $result,
        string $filterName,
        string $value = null
    ) : string {
        $urlParameters = $this->generateQueryUrlParameters($result);

        if (
            is_null($value) ||
            !isset($urlParameters[$filterName])
        ) {
            unset($urlParameters[$filterName]);
        } elseif (($key = array_search($value, $urlParameters[$filterName])) !== false) {
            unset($urlParameters[$filterName][$key]);
        }

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Change price range.
     *
     * @param Result $result
     *
     * @return string
     */
    public function removePriceRangeFilter(Result $result) : string
    {
        $urlParameters = $this->generateQueryUrlParameters($result);
        unset($urlParameters['price']);

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Set pagination.
     *
     * @param Result $result
     * @param int    $page
     *
     * @return string
     */
    public function addPage(
        Result $result,
        int $page
    ) : string {
        $urlParameters = $this->generateQueryUrlParameters($result);
        $urlParameters['page'] = $page;

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Add previous page.
     *
     * @param Result $result
     *
     * @return null|string
     */
    public function addPrevPage(Result $result) : ? string
    {
        $query = $result->getQuery();
        $urlParameters = $this->generateQueryUrlParameters($result);
        $page = $query->getPage();
        $prevPage = $page - 1;

        if ($prevPage < 1) {
            return null;
        }

        $urlParameters['page'] = $prevPage;

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Add next page.
     *
     * @param Result $result
     *
     * @return null|string
     */
    public function addNextPage(Result $result) : ? string
    {
        $query = $result->getQuery();
        $urlParameters = $this->generateQueryUrlParameters($result);
        $page = $query->getPage();
        $nextPage = $page + 1;

        if ((($nextPage - 1) * $query->getSize()) > $result->getTotalHits()) {
            return null;
        }

        $urlParameters['page'] = $nextPage;

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Add sort by. Return null if doesn't change.
     *
     * @param Result $result
     * @param string $field
     * @param string $mode
     *
     * @return string
     */
    public function addSortBy(
        Result $result,
        string $field,
        string $mode
    ) : ? string {
        $urlParameters = $this->generateQueryUrlParameters($result);

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

        return $this->createUrlByUrlParameters(
            $result,
            $urlParameters
        )['route'];
    }

    /**
     * Query to url parameters.
     *
     * @param Result $result
     * @param string $filterName
     *
     * @return array
     */
    private function generateQueryUrlParameters(
        Result $result,
        string $filterName = null
    ) : array {
        $query = $result->getQuery();
        $queryFilters = $query->getFilters();
        foreach ($queryFilters as $currentFilterName => $filter) {
            /**
             * Special case for elements with LEVEL.
             */
            $urlParameters[$currentFilterName] = (
                !is_null($filterName) &&
                $currentFilterName === $filterName &&
                $filter->getApplicationType() === Filter::MUST_ALL_WITH_LEVELS
            )
                ? []
                : $filter->getValues();
        }

        unset($urlParameters['_query']);

        $queryString = $query
            ->getFilter('_query')
            ->getValues()[0];

        if (!empty($queryString)) {
            $urlParameters['q'] = $queryString;
        }

        $sort = $query->getSortBy();
        if ($sort !== SortBy::SCORE) {
            $urlParameters['sort_by'] = $sort;
        }

        return $urlParameters;
    }

    /**
     * Generate url by query parameters.
     *
     * Given a route dictionary, we should apply the first one encontered
     *
     * @param Result $result
     * @param array  $urlParameters
     *
     * @return string[]
     */
    private function createUrlByUrlParameters(
        Result $result,
        array $urlParameters
    ) : array {
        foreach ($this->routesDictionary as $field => $route) {
            if (
                isset($urlParameters[$field]) &&
                (
                    !is_array($urlParameters[$field]) ||
                    count($urlParameters[$field]) === 1
                )
            ) {
                $value = is_array($urlParameters[$field])
                    ? reset($urlParameters[$field])
                    : $urlParameters[$field];

                unset($urlParameters[$field]);
                $path = $this
                    ->router
                    ->getRouteCollection()
                    ->get($route)
                    ->getPath();
                preg_match_all(
                    '~\{(.+?)\}~',
                    $path,
                    $matches
                );

                return [
                    'route' => urldecode($this
                        ->router
                        ->generate(
                            $route,
                            array_merge(
                                array_intersect_key(
                                    $result
                                        ->getAggregation($field)
                                        ->getAllElements()[$value]
                                        ->getValues(),
                                    array_flip($matches[1])
                                ),
                                $urlParameters
                            ),
                            UrlGeneratorInterface::ABSOLUTE_URL
                        )),
                    'url_parameters' => $urlParameters,
                    'template_path' => $path,
                    'field' => $field,
                ];
            }
        }

        return [
            'route' => urldecode($this
                ->router
                ->generate(
                    $this->routesDictionary['main'],
                    $urlParameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                )),
            'url_parameters' => $urlParameters,
            'template_path' => urldecode($this
                ->router
                ->generate(
                    $this->routesDictionary['main'],
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ),
            'field' => false,
        ];
    }
}
