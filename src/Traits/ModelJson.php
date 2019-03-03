<?php

namespace Krasnikov\EloquentJSON\Traits;

use DateTimeInterface;
use Illuminate\Support\Facades\Config;
use Krasnikov\EloquentJSON\Json;

trait ModelJson
{
    /**
     * @return array
     */
    public function toJsonSpec()
    {
        return (new Json($this))->toJson();
    }

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(Config::get('jsonSpec.date_format'));
    }
}
