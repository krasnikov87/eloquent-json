<?php

namespace Krasnikov\EloquentJSON\Traits;

use DateTimeInterface;
use Illuminate\Support\Facades\Config;
use Krasnikov\EloquentJSON\IncludeScope;
use Krasnikov\EloquentJSON\Json;

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
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(Config::get('jsonSpec.date_format'));
    }
}
