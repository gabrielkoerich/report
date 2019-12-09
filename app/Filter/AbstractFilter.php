<?php

namespace App\Filter;

use App\Meta;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter
{
    /**
     * Get the resource name.
     */
    public function filterName(): string
    {
        $class = basename(str_replace('\\', '/', get_called_class()));

        return str_replace('\\', '', strtolower($class));
    }

    /**
     * Check if a given parameter should apply this filter.
     */
    protected function shouldApply($parameter)
    {
        $field = Arr::get($parameter, 'field');

        if (! is_array($parameter)) {
            return false;
        }

        $query = Arr::get($parameter, 'query');

        if (is_null($query) || $query == '') {
            return false;
        }

        return Meta::where('name', $field)
            ->where('type', $this->filterName())
            ->exists();
    }

    /**
     * Apply this filter to the query builder.
     */
    public function apply(Builder $builder, array $parameters): void
    {
        foreach ($parameters as $index => $parameter) {
            if (! $this->shouldApply($parameter)) {
                continue;
            }

            $operator = Arr::get($parameter, 'operator');

            if (empty($operator)) {
                $operator = '=';
            }

            $query = $this->parseQuery(Arr::get($parameter, 'query'), $operator);

            if (Str::contains($operator, ['has', 'starts', 'ends'])) {
                $operator = str_replace(['!', 'has', 'starts', 'ends'], ['not ', 'like', 'like', 'like'], $operator);
            }

            $field = Arr::get($parameter, 'field');

            $this->applyWhere($builder, $field, $operator, $query);
        }
    }

    /**
     * Check if operator is opposite.
     */
    protected function isOpposite($operator): bool
    {
        return Str::contains($operator, ['<>', 'not']);
    }

    /**
     * Parse the given query.
     */
    protected function parseQuery($query, $operator)
    {
        if (Str::contains($query, '|')) {
            $query = explode('|', $query);
        }

        return array_map(function ($query) use ($operator) {
            $query = trim($query);
            $query = str_replace('%%', '%', $query);
            $query = is_numeric($query) ? $query * 1 : $query;

            switch ($operator) {
                case 'has':
                case '!has':
                    $query = str_replace(' ', '%', '%' . $query . '%');

                break;
                case 'starts':
                    $query = str_replace(' ', '%', $query . '%');

                break;
                case 'ends':
                    $query = str_replace(' ', '%', '%' . $query);

                break;
            }

            return $query;
        }, Arr::wrap($query));
    }

    /**
     * Apply the where filter.
     */
    protected function applyWhere(Builder $builder, $field, $operator, $query)
    {
        $builder->where($field, $operator, $query);
    }

    /**
     * Get the operators
     */
    public function getOperators()
    {
        if ($this->hideOperators === true) {
            return [];
        }

        if (isset($this->operators)) {
            return $this->operators;
        }

        return Filterable::getFilterOperators();
    }
}
