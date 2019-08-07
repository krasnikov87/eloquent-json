<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Model;
use Krasnikov\EloquentJSON\Traits\ModelJson;

/**
 * Class JsonModel
 * @package Krasnikov\EloquentJSON
 */
class JsonModel extends Model
{
    use ModelJson;
}
