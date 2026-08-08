<?php

namespace App\Core\Helpers;

use App\Core\Enums\RequestQueryOperatorsEnum;
use App\Core\Enums\SqlOrderDirectionEnum;
use App\Core\Enums\SqlQueryOperatorsEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ModelHelpers
{
    public static array $operatorDictionary = [
        RequestQueryOperatorsEnum::Equal->value => SqlQueryOperatorsEnum::Equal,
        RequestQueryOperatorsEnum::Like->value => SqlQueryOperatorsEnum::Like,
        RequestQueryOperatorsEnum::LessThan->value => SqlQueryOperatorsEnum::LessThan,
        RequestQueryOperatorsEnum::LessThanEqual->value => SqlQueryOperatorsEnum::LessThanEqual,
        RequestQueryOperatorsEnum::GreaterThan->value => SqlQueryOperatorsEnum::GreaterThan,
        RequestQueryOperatorsEnum::GreaterThanEqual->value => SqlQueryOperatorsEnum::GreaterThanEqual,
        RequestQueryOperatorsEnum::NotEqual->value => SqlQueryOperatorsEnum::NotEqual
    ];

    public static function getColumnsFromTable(string $tableName): array {
        $databaseFields = Schema::getColumns($tableName);
        $fields = [];

        $typesMap = [
            'varchar' => ['type' => 'string', 'default' => ''],
            'char' => ['type' => 'string', 'default' => ''],
            'text' => ['type' => 'string', 'default' => ''],
            'blob' => ['type' => 'string', 'default' => ''],
            'int' => ['type' => 'int', 'default' => 0],
            'int8' => ['type' => 'int', 'default' => 0],
            'integer' => ['type' => 'int', 'default' => 0],
            'bigint' => ['type' => 'int', 'default' => 0],
            'float' => ['type' => 'float', 'default' => 0],
            'double' => ['type' => 'float', 'default' => 0],
            'decimal' => ['type' => 'float', 'default' => 0],
            'numeric' => ['type' => 'float', 'default' => 0],
            'bit' => ['type' => 'bool', 'default' => false],
            'tinyint' => ['type' => 'bool', 'default' => false],
            'bool' => ['type' => 'bool', 'default' => false],
            'boolean' => ['type' => 'bool', 'default' => false],
            'timestamp' => ['type' => 'Carbon', 'default' => null],
            'date' => ['type' => 'Carbon', 'default' => null],
            'datetime' => ['type' => 'Carbon', 'default' => null]
        ];

        foreach ($databaseFields as $databaseField) {
            preg_match('/\((.*?)\)/', $databaseField['type'], $matches);
            $size = explode(',', $matches[1] ?? '');
            $maxLength = (int) ($size[0] ?? 0);
            $precision = (int) ($size[1] ?? 0);

            $fields[] = [
                'name' => $databaseField['name'],
                ...$typesMap[$databaseField['type_name']],
                'nullable' => $databaseField['nullable'],
                'max_length' => $maxLength,
                'precision' => $precision,
            ];
        }

        return $fields;
    }

    public static function setFiltersOnQuery(Builder $query, array $filters = [], array $columnsToFilter = []): Builder {
        $columns = ModelHelpers::getColumnsFromTable($query->getModel()->getTable());
        $columns = collect($columns);

        foreach ($filters as $columnName => $operations) {
            if (count($columnsToFilter) > 0 && !in_array($columnName, $columnsToFilter)) {
                continue;
            }

            $column = $columns->firstWhere('name', '=', $columnName);

            if (!$column) {
                continue;
            }

            foreach ($operations as $operator => $value) {
                $operatorEnum = RequestQueryOperatorsEnum::tryFrom($operator);

                if (!$operatorEnum) {
                    continue;
                }

                $sqlOperatorEnum = self::$operatorDictionary[$operatorEnum->value];

                if ($column['type'] == 'Carbon') {
                    $value = Carbon::parse($value);
                } elseif ($sqlOperatorEnum == SqlQueryOperatorsEnum::Like) {
                    $value = "%$value%";
                }
                
                $query->where($column['name'], $sqlOperatorEnum->value, $value);
            }
        }
        
        return $query;
    }

    public static function setSortsOnQuery(Builder $query, string $sortOptions): Builder {
        if (empty($sortOptions)) {
            return $query;
        }

        $sorts = explode(',', $sortOptions);

        $columns = ModelHelpers::getColumnsFromTable($query->getModel()->getTable());
        $columns = collect($columns);

        foreach ($sorts as $sort) {
            $sortDirection = str_starts_with($sort, '-') ? SqlOrderDirectionEnum::Descending : SqlOrderDirectionEnum::Ascending;

            $columnName = ltrim($sort, '-'); 
            $column = $columns->firstWhere('name', '=', $columnName);

            if (!$column) {
                continue;
            }

            $query->orderBy($column['name'], $sortDirection->value);
        }

        return $query;
    }
}