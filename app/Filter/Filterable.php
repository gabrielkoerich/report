<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public static $filters = [
        Text::class,
        Datetime::class,
    ];

    private static $filterOperators = [
        'has' => 'Has',
        '=' => 'Equals',
        '!has' => 'Doens\'t have',
        '<>' => 'Not equal',
        '>' => 'Larger then',
        '<' => 'Less then',
        '>=' => 'Larger or equal',
        '<=' => 'Less or equal',
        'starts' => 'Starts with',
        'ends' => 'Ends with',
    ];

    /**
     * Get the filters.
     */
    public static function getFilters(): array
    {
        return isset(static::$filters) ? static::$filters : [];
    }

    /**
     * Get the operators.
     */
    public static function getFilterOperators(): array
    {
        return static::$filterOperators;
    }

    /**
     * Set the filters.
     */
    public static function setFilters(array $filters): void
    {
        static::$filters = $filters;
    }

    /**
     * Filter by parameters.
     */
    public static function filterBy($parameters): Builder
    {
        $builder = (new static)->newQuery();

        if (! is_array($parameters) || empty($parameters)) {
            return $builder;
        }

        foreach (static::getFilters() as $class) {
            $filters[] = $filter = new $class;
        }

        // Set filter instances.
        static::setFilters($filters);

        return $builder->where(function ($builder) use ($parameters) {
            foreach (static::getFilters() as $filter) {
                $filter->apply($builder, $parameters);
            }

            return $builder;
        });
    }
}
