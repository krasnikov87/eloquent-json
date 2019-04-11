<?php

namespace App\Domain\Core;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Sorting
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    const SORT_DIRS = [
        self::SORT_ASC,
        self::SORT_DESC,
    ];

    const DEFAULT_SORT_FIELD = 'created_at';

    const DESC_SORT_SYMBOL = '-';

    /**
     * @var array
     */
    private $sort;

    /**
     * Sorting constructor.
     * @param array $sort
     */
    public function __construct(array $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @param Builder $builder
     */
    public function setBuilder(Builder $builder): void
    {
        foreach ($this->sort as $field) {
            $builder->orderBy($field['sort'], $field['dir']);
        }
    }

    /**
     * @param Request $request
     * @return Sorting
     */
    public static function fromRequest(Request $request): Sorting
    {
        $sort = $request->get('sort');
        if (!$sort) {
            return new Sorting([
                'field' => self::DEFAULT_SORT_FIELD,
                'dir' => self::SORT_DESC
            ]);
        }

        $fields = collect(explode(',', $sort))->map(function (string $field) {
            if (mb_substr($field, 0, 1) == self::DESC_SORT_SYMBOL) {
                return [
                    'field' => mb_substr($field, 1),
                    'dir' => self::SORT_DESC
                ];
            }
            return [
                'field' => $field, 'dir' => self::SORT_ASC
            ];
        })->toArray();

        return new Sorting($fields);
    }
}
