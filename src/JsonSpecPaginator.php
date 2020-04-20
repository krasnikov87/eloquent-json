<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class JsonSpecPaginator
 * @package Krasnikov\EloquentJSON
 */
class JsonSpecPaginator extends LengthAwarePaginator
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $res = [
            'data' => $this->items->toArray(),
            'meta' => [
                'total' => $this->total,
                'perPage' => $this->perPage,
            ],
        ];

        return $res;
    }

    /**
     * Get the URL for a given page number.
     *
     * @param int $page
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = [
            'page' => [
                'number' => $page,
                'size' => $this->perPage,
            ]
        ];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path
            . (Str::contains($this->path, '?') ? '&' : '?')
            . Arr::query($parameters)
            . $this->buildFragment();
    }
}
