<?php

declare(strict_types=1);

namespace Krasnikov\EloquentJSON;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection as DBCollection;

/**
 * Class JsonCollection
 * @package Krasnikov\EloquentJSON
 */
class JsonSpecCollection extends DBCollection
{
    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->map(function ($value) {
            return $value instanceof Arrayable ? $value->toJsonSpec() : $value;
        })->all();
    }
}
