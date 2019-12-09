<?php

namespace App\Filter;

use DB;
use Illuminate\Database\Eloquent\Builder;

class Datetime extends AbstractFilter
{
    /**
     * Apply the filter.
     */
    protected function applyWhere(Builder $builder, $field, $operator, $query)
    {
        $query = $query[0];

        switch ($query) {
            case 'YEAR(NOW())':
                $field = DB::raw('YEAR(' . $field . ')');
                $query = DB::raw('YEAR(NOW())');
        }

        $builder->where($field, $operator, $query);
    }
}
