<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilter
{
    public function __construct(
        private Request $request
    ) {}

    public function apply(Builder $query): Builder
    {
        $this->withRelations($query);

        return $query;
    }

    protected function withRelations(Builder $query): void
    {
        $with = $this->request->get('with');

        if ($with && str_contains($with, 'category')) {
            $query->with('productCategory');
        }

        if ($with && str_contains($with, 'slots')) {
            $query->with('slots');
        }
    }
}
