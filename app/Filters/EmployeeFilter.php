<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EmployeeFilter
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

        if ($with && str_contains($with, 'classification')) {
            $query->with('classification');
        }

        if ($with && str_contains($with, 'card')) {
            $query->with('card');
        }

        if ($with && str_contains($with, 'transactions')) {
            $query->with('transactions');
        }
    }
}
