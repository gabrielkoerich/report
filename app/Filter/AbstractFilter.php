<?php

namespace App\Filter;

use App\Meta;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter
{
    /**
     * The filtered field.
     *
     * @var string
     */
    public $field;

    /**
     * The filter description.
     *
     * @var string
     */
    public $description;

    /**
     * The filter options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Whether the filter has added a join.
     *
     * @var bool
     */
    protected $joined = 0;

    /**
     * The number of times this filter was executed in the current request.
     */
    protected $applied = 0;

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
     * Join the filter.
     */
    public function join(Builder $builder, array $parameters)
    {
        if (! method_exists($this, 'applyJoin')) {
            return false;
        }

        foreach ($parameters as $index => $parameter) {
            if (! $this->shouldApply($parameter)) {
                continue;
            }

            $this->uniqueAliases[] = str_random(5);

            if ($this->multipleJoin === true || $this->joined === 0) {
                $this->applyJoin($builder, $parameter);
            }

            $this->joined += 1;
        }
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

            $this->applied += 1;
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
     * Get the filter options.
     */
    public function getOptions()
    {
        return $this->options;
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

    /**
     * Check if filter is multiple.
     */
    public function isMultiple(): bool
    {
        return $this->multiple === true;
    }

    /**
     * Get the multiple filter fields.
     */
    public function getMultiple()
    {
        return [];
    }
}
