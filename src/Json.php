<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Class Json
 * @package Krasnikov\EloquentJSON
 */
class Json
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $type;
    /**
     * @var array
     */
    private $attributes = [];
    /**
     * @var array
     */
    private $relationships = [];
    /**
     * @var array
     */
    private $included = [];

    /**
     * Json constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->type = $this->getClassName();
        $this->id = $this->getId();
        $this->attributes = $this->getAttributes($this->model);
        $this->relationships = $this->setRelationships(null);
        $this->included = $this->setIncluded(null)->toArray();
    }

    /**
     * @return array
     */
    public function toJson(): array
    {
        $data = [
            'data' => [
                'type' => $this->type,
                'id' => $this->id,
                'attributes' => $this->attributes,
            ]
        ];
        if ($this->relationships) {
            $data['data']['relationships'] = $this->relationships;
        }

        if ($this->included) {
            $data['included'] = $this->included;
        }

        return $data;
    }

    /**
     * @param Model $model
     * @return array
     */
    private function getAttributes(Model $model): array
    {
        $response = [];
        $hidden = $model->hidden ?: [];
        if (!config('jsonSpec.show_id')) {
            $hidden[] = 'id';
        }
        foreach ($model->attributesToArray() as $key => $attribute) {
            if (in_array($key, $hidden) || $attribute === null) {
                continue;
            }
            $name = $this->snakeToCamelCase($key);
            $fields = request('fields');
            $class = $this->getClassName($model);
            if (!isset($fields[$class])) {
                $fields = null;
            }
            if ($fields && isset($fields[$class]) && in_array($key, explode(',', $fields[$class]))) {
                $response[$name] = $this->getTranslate($model, $key);
            } elseif ($fields && isset($fields[$class]) && in_array($name, explode(',', $fields[$class]))) {
                $response[$name] = $this->getTranslate($model, $key);
            }
            if (!$fields || !isset($fields[$class])) {
                $response[$name] = $this->getTranslate($model, $key);
            }
        }
        return $response;
    }

    /**
     * @param Model $model
     * @param string $key
     * @return mixed
     */
    private function getTranslate(Model $model, string $key)
    {
        if (request('translations') && in_array($key, $model->translatable)) {
            return $model->getTranslations($key);
        }
        return $model->$key;
    }

    /**
     * @param Model|null $class
     * @return string
     */
    private function getClassName(?Model $class = null): string
    {
        if (!$class) {
            $class = $this->model;
        }
        if ($class->className) {
            return $class->className;
        }

        return strtolower(class_basename($class));
    }

    /**
     * @param $key
     * @return string
     */
    private function snakeToCamelCase($key): string
    {
        return lcfirst(str_replace('_', "", ucwords($key, "/_")));
    }

    /**
     * @return mixed
     */
    private function getId()
    {
        return $this->model->id;
    }

    /**
     * @return array
     */
    private function setRelationships(?Model $model): array
    {
        if (!$model) {
            $model = $this->model;
        }

        $relations = [];
        collect($model->getRelations())->each(function ($item, string $key) use (&$relations) {
            if ($item instanceof Model) {
                $rel = $this->getRelationsForOneItem($item);
                if ($rel) {
                    $relations[$key]['data'] = $rel;
                }
                return;
            }

            if (is_array($item)) {
                $item = collect($item);
            }

            if ($item instanceof Collection) {
                $relations[$key]['data'] = $item->map(function ($item) {
                    return $this->getRelationsForOneItem($item);
                })->filter()->values();
            }
        });
        return $relations;
    }

    /**
     * @param Model $model
     * @return array|null
     */
    private function getRelationsForOneItem(Model $model)
    {
        if (!isset($model->id)) {
            return null;
        }
        $rel = null;
        if ($model->getRelations()) {
            $rel = $this->setRelationships($model);
        }

        $res = [
            'id' => $model->id,
            'type' => $this->getClassName($model),
        ];

        if (count($rel ?? [])) {
            $res['relations'] = $rel;
        }
        return $res;
    }

    /**
     * @return void
     */
    private function setIncluded(?Model $model): SupportCollection
    {
        if (!$model) {
            $model = $this->model;
        }
        $included = collect();
        collect($model->getRelations())->map(function ($item) use ($included) {
            if ($item instanceof Model) {
                $this->getIncludeForOneItem($item)->each(function ($inc) use ($included) {
                    $included->push($inc);
                });

                return;
            }

            if (is_array($item)) {
                $item = collect($item);
            }

            if ($item instanceof Collection) {
                collect($item)->each(function ($item) use ($included) {
                    $this->getIncludeForOneItem($item)->each(function ($inc) use ($included) {
                        $included->push($inc);
                    });
                });
            }
        });
        return $included;
    }

    /**
     * @param Model $model
     * @return array
     */
    private function getIncludeForOneItem(Model $model): SupportCollection
    {
        $included = collect();
        if (!$model->id) {
            return collect();
        }
        $this->setIncluded($model)->each(function ($inc) use ($included) {
            $included->push($inc);
        });

        $included->push([
            'id' => $model->id,
            'type' => $this->getClassName($model),
            'attributes' => $this->getAttributes($model)
        ]);
        return $included;
    }
}
