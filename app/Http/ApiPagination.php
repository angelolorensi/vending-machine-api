<?php

namespace App\Http;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiPagination
{
    public function __construct(
        private Request $request
    ) {}

    public function paginate(
        Builder $query,
        string $resourceClass,
        ?object $filter = null
    ): AnonymousResourceCollection {
        if ($filter) {
            $query = $filter->apply($query);
        }

        $perPage = (int) $this->request->get('per_page', 15);
        $page = (int) $this->request->get('page', 1);

        $paginatedData = $query->paginate($perPage, ['*'], 'page', $page);

        return $resourceClass::collection($paginatedData);
    }
}
