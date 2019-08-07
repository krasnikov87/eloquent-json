<?php

namespace Krasnikov\EloquentJSON\Traits;

use Krasnikov\EloquentJSON\Builder;
use Krasnikov\EloquentJSON\IncludeScope;
use Krasnikov\EloquentJSON\Json;
use Sofa\Eloquence\Query\Builder as QueryBuilder;


/**
 * Trait ModelJson
 * @package Krasnikov\EloquentJSON\Traits
 */
trait ModelJson
{
    /**
     * @return void
     */
    public static function bootModelJson(): void
    {
        static::addGlobalScope(new IncludeScope);
    }
    /**
     * @return array
     */
    public function toJsonSpec(): array
    {
        return (new Json($this))->toJson();
    }

    /**
     * @return array
     */
    public function allowedReferences(): array
    {
        return $this->allowedReferences ?? [];
    }

    /**
     * @return Builder
     */
    public function newJsonQuery()
    {
        return (new Builder(
            $this->newJsonQueryBuilder()
        ))->setModel($this);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Sofa\Eloquence\Query\Builder
     */
    protected function newJsonQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
    }
}
