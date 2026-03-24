<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

readonly class IndexResponse implements Responsable
{
    public function __construct(
        protected array $data,
        protected mixed $dto,
        protected int $total,
    ) {
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: $this->toData(),
            status: ResponseAlias::HTTP_OK,
            json: false,
        );
    }

    protected function toData(): array
    {
        $array = [];

        $array['data'] = $this->data;
        $array['links'] = $this->toLinks();
        $array['meta'] = $this->toMeta();

        return $array;

    }

    protected function toLinks(): array
    {
        $links = [];
        $link = $this->dto->toLink(true);

        $lastPage = (int)ceil($this->total / $this->dto->perPage);

        $prefix = str_contains($link, '?') ? '&' : '?';

        $links['first'] = $link;
        $links['last'] = $link . $prefix . 'page=' . $lastPage;

        $links['prev'] = $this->dto->page > 1 ? $link . $prefix . 'page=' . ($this->dto->page - 1) : null;

        $links['next'] = $this->dto->page < $lastPage ? $link . $prefix . 'page=' . ($this->dto->page + 1) : null;

        return $links;
    }

    protected function toMeta(): array
    {
        $meta = [];
        $meta['current_page'] = $this->dto->page;
        $meta['last_page'] = ceil($this->total / $this->dto->perPage);
        $meta['path'] = $this->dto->toPath();
        $meta['per_page'] = $this->dto->perPage;
        $meta['total'] = $this->total;

        return $meta;
    }

    protected function toPagination(): array
    {
        $pagination = [];
        $pagination['current_page'] = $this->dto->page;
        $pagination['last_page'] = ceil($this->total / $this->dto->perPage);
        $pagination['per_page'] = $this->dto->perPage;
        $pagination['total'] = $this->total;

        return $pagination;
    }
}
