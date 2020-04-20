<?php

namespace Krasnikov\EloquentJSON\Traits;

use Krasnikov\EloquentJSON\Builder;
use Krasnikov\EloquentJSON\Json;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Krasnikov\EloquentJSON\JsonSpecCollection;

/**
 * Trait ModelJson
 * @package Krasnikov\EloquentJSON\Traits
 */
trait ModelJson
{
    /**
     * @return array
     */
    public function toJsonSpec(): array
    {
        return (new Json($this))->toJson();
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        return (new Builder(
            $this->newJsonQueryBuilder()
        ))->setModel($this);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     */
    protected function newJsonQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new JsonSpecCollection($models);
    }
}
