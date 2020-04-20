<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Container\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as IlluminateBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class Builder
 * @package Krasnikov\EloquentJSON
 */
class Builder extends IlluminateBuilder
{
    /**
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator|JsonSpecPaginator|mixed
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator(
            $results->toArray(),
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }


    /**
     * @param Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return JsonSpecPaginator|mixed
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(
            JsonSpecPaginator::class,
            compact(
                'items',
                'total',
                'perPage',
                'currentPage',
                'options'
            )
        );
    }
}
