<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Validation\ValidationException;

/**
 * Class IncludeScope
 * @package Krasnikov\EloquentJSON
 */
class IncludeScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (request('include')) {
            $includes = explode(',', request('include'));

            $this->checkAllowed($model, $includes);
            $builder->with($includes);
            return;
        }
    }

    /**
     * @param Model $model
     * @param array $includes
     */
    private function checkAllowed(Model $model, array $includes)
    {
        $diff = array_diff($includes, $model->allowedReferences());
        if (count($diff)) {
            throw ValidationException::withMessages([
                'includes' => [__('validation.in_array', [
                    'attribute' => 'includes',
                    'other' => implode(', ', $model->allowedReferences())
                ])],
            ]);
        }
    }
}
