<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

/**
 * Class Pagination
 * @package Krasnikov\EloquentJSON
 */
class Pagination
{
    /**
     * Default page and per page values.
     */
    const DEFAULT_PAGE = 1;
    const DEFAULT_PER_PAGE = 10;

    const PER_PAGE_LIST = [
        5 => 5,
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100,
    ];

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $perPage;

    /**
     * Pagination constructor.
     * @param int $page
     * @param int $perPage
     */
    public function __construct(int $page = self::DEFAULT_PAGE, int $perPage = self::DEFAULT_PER_PAGE)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function page(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        return $this->perPage * ($this->page - 1);
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->perPage;
    }

    /**
     * @param Request $request
     * @return Pagination
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function fromRequest(Request $request): Paginator
    {
        return new Pagination(self::getPageNumberFromRequest($request), self::getPerPageFromRequest($request));
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getPageNumberFromRequest(Request $request): int
    {
        $requestPage = $request->get('page');

        if (!$requestPage) {
            return self::DEFAULT_PAGE;
        }

        return $requestPage['number'];
    }

    /**
     * @param Request $request
     * @return int
     */
    public static function getPerPageFromRequest(Request $request): int
    {
        $requestPage = $request->get('page');

        if (!$requestPage) {
            return self::DEFAULT_PER_PAGE;
        }

        return $requestPage['size'];
    }
}
