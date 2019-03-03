<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Model;
use Krasnikov\EloquentJSON\Traits\ModelJson;

class JsonModel extends Model
{
    use ModelJson;
}
