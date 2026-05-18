<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class QueryFilters
{
    public static function whereLike(Builder $query, string $column, string $value, string $boolean = 'and'): Builder
    {
        $wrappedColumn = $query->getQuery()->grammar->wrap($column);

        return $query->whereRaw(
            "LOWER({$wrappedColumn}) LIKE ?",
            ['%' . mb_strtolower($value) . '%'],
            $boolean
        );
    }

    public static function orWhereLike(Builder $query, string $column, string $value): Builder
    {
        return self::whereLike($query, $column, $value, 'or');
    }

    public static function whereAnyLike(Builder $query, array $columns, string $value): Builder
    {
        return $query->where(function (Builder $inner) use ($columns, $value) {
            foreach (array_values($columns) as $index => $column) {
                self::whereLike($inner, $column, $value, $index === 0 ? 'and' : 'or');
            }
        });
    }

    public static function whereRoleAlias(Builder $query, array $aliases): Builder
    {
        return $query->where(function (Builder $inner) use ($aliases) {
            foreach (array_values($aliases) as $index => $alias) {
                self::whereLike($inner, 'nama_role', $alias, $index === 0 ? 'and' : 'or');
            }
        });
    }
}
