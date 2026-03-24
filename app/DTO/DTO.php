<?php

namespace App\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 *  Base class for DTO-classes
 *  DTO - Data Transfer Object, a class that contains data that is transferred
 *        between the method index () of the controller and the repository.
 */
abstract readonly class DTO
{
    public const int PER_PAGE = 20;

    /**
     * Page number for pagination. Default 1
     * @var int
     */
    public int $page;

    /**
     * Number of items per page. Default self::PER_PAGE
     * @var int
     */
    public int $perPage;

    /**
     * Search query string. Default null
     * @var string|null
     */
    public ?string $query;

    /**
     * Sorting column name. Default $this->getSortNameDefault()
     * @var string
     */
    public string $sortName;

    /**
     * Sorting direction. Default 'asc'
     * @var string
     */
    public string $sortDir;

    abstract protected function getSortNameDefault(): string;

    abstract protected function getRouteName(): string;

    public function __construct(Request $request)
    {
        $this->fromRequest($request);
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this as $name => $value) {
            if ($name == 'routeName') {
                continue;
            }
            $array[$name] = $value;
        }

        return $array;
    }

    public function toPath(): string
    {
        return route($this->getRouteName());
    }

    public function toLink(bool $withoutPage = false): string
    {
        $defaults = $this->getDefaults();

        $args = [];
        foreach ($this as $name => $value) {
            if ($name == 'routeName' || $value == $defaults[$name] || ($withoutPage && $name == 'page')) {
                continue;
            }
            $args[$name] = $value;
        }

        $query = http_build_query($args);
        if ($query !== '') {
            $query = '?'.$query;
        }

        return route($this->getRouteName()).$query;
    }

    protected function fromRequest(Request $request): void
    {
        $validator = Validator::make($request->all(), $this->getRules($request));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $defaults = $this->getDefaults();

        $this->page = $request->input('page', $defaults['page']);
        $this->perPage = $request->input('per_page', $defaults['perPage']);
        $this->query = $request->input('query', $defaults['query']);
        $this->sortName = $request->input('sort_name', $defaults['sortName']);
        $this->sortDir = $request->input('sort_dir', $defaults['sortDir']);

    }

    protected function getRules(Request $request): array
    {

        return [
            'page' => 'int|min:1',
            'per_page' => 'int|min:0',
            'sort_dir' => 'in:asc,desc',
            'sort_name' => 'string',
            'query' => 'string',
        ];
    }

    protected function getDefaults(): array
    {
        return [
            'page' => 1,
            'perPage' => self::PER_PAGE,
            'sortDir' => 'asc',
            'sortName' => $this->getSortNameDefault(),
            'query' => null,
        ];
    }
}
