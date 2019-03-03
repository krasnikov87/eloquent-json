<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Database\Eloquent\Model;

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
     * @var array
     */
    private $links = [];

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
        $this->setRelationships();
        $this->setIncluded();
        $this->links = $this->getLinks($model);
    }

    /**
     * @return array
     */
    public function toJson()
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
    private function getAttributes(Model $model)
    {
        $response = [];
        foreach ($model->attributesToArray() as $key => $attribute) {
            $key = $this->snakeToCamelCase($key);
            if (in_array($key, array_merge($model->hidden ?: [], ['id']))) {
                continue;
            }
            $fields = request('fields');
            $class = $this->getClassName($model);
            if (!isset($fields[$class])) {
                $fields = null;
            }
            if ($fields && isset($fields[$class]) &&  in_array($key, explode(',', $fields[$class]))) {
                $response[$key] = $this->getTranslate($model, $key);
            }
            if (!$fields || !isset($fields[$class])) {
                $response[$key] = $this->getTranslate($model, $key);
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
     * @return mixed|string
     */
    private function getClassName(?Model $class = null)
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
     * @return mixed
     */
    private function snakeToCamelCase($key)
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
     * @return void
     */
    private function setRelationships()
    {
        collect($this->model->getRelations())->each(function (?Model $item, string $key) {
            if ($item instanceof Model) {
                $this->relationships[$key]['data'] = [
                    'id' => $item->id,
                    'type' => $this->getClassName($item),
                ];
            }
            if (is_array($item)) {
                $this->relationships[$key]['data'] = collect($item)->map(function ($item) use ($key) {
                    return [
                        'id' => $item['id'],
                        'type' => $this->getClassName($item),
                    ];
                });
            }
        });
    }

    /**
     * @return void
     */
    private function setIncluded()
    {
        collect($this->model->getRelations())->map(function (?Model $item) {
            if ($item instanceof Model) {
                $this->included[] = [
                    'id' => $item->id,
                    'type' => $this->getClassName($item),
                    'attributes' => $this->getAttributes($item)
                ];
            }
            if (is_array($item)) {
                 collect($item)->each(function ($item) {
                     $this->included[] = [
                        'id' => $item['id'],
                        'type' => $this->getClassName($item),
                         'attributes' => $this->getAttributes($item)
                     ];
                 });
            }
        });
    }

    /**
     * @param Model $model
     */
    private function getLinks(Model $model)
    {

    }
}
