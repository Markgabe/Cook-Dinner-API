<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestDataExtractor
{
    private function searchRequestData(Request $request)
    {
        $queryString = $request->query->all();
        $ordinationData = array_key_exists('sort', $queryString)
            ? $queryString['sort']
            : null;
        unset($queryString['sort']);
        $currentPage = array_key_exists('page', $queryString)
            ? $queryString['page']
            : 1;
        unset($queryString['page']);
        $itemsPerPage = array_key_exists('amount', $queryString)
            ? $queryString['amount']
            : 5;
        unset($queryString['amount']);

        return [$queryString, $ordinationData, $currentPage, $itemsPerPage];
    }

    public function searchOrdinationData(Request $request)
    {
        [, $ordination] = $this->searchRequestData($request);
        return $ordination;
    }

    public function searchFilterData(Request $request)
    {
        [$filter, ] = $this->searchRequestData($request);
        return $filter;
    }

    public function searchPaginationData(Request $request)
    {
        [, , $currentPage, $itemsPerPage] = $this->searchRequestData($request);
        return [$currentPage, $itemsPerPage];
    }
}
