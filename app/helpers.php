<?php

use Illuminate\Support\Str;

if (!function_exists('pagination_count')) {
    /**
     * Return a formatted pagination count string for a paginator instance.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param string $itemLabel
     * @return string
     */
    function pagination_count($paginator, $itemLabel = 'result')
    {
        if ($paginator->total() === 0) {
            return __('No :itemLabel found', ['itemLabel' => Str::plural($itemLabel)]);
        }
        return __('Showing :from to :to of :total :itemLabel', [
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
            'itemLabel' => Str::plural($itemLabel, $paginator->total()),
        ]);
    }
} 