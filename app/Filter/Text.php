<?php

namespace App\Filter;

use DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Text extends AbstractFilter
{
    /**
     * Apply the filter.
     */
    protected function applyWhere(Builder $builder, $field, $operator, $query)
    {
        if (count($query) === 1) {
            return $query->where($field, $operator, $query[0]);
        }

        $queries = $query;

        $builder->where(function ($builder) use ($field, $operator, $queries) {
            foreach ($queries as $query) {
                $builder->orWhere($field, $operator, $query);
            }
        });
    }
}
