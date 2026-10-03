<?php

namespace App\Core\Helpers;

use App\Core\Enums\RequestQueryOperatorsEnum;
use App\Core\Enums\SqlOrderDirectionEnum;
use App\Core\Enums\SqlQueryOperatorsEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Stringable;
use Throwable;

class ModelHelpers
{
    public const DEFAULT_SORTS = '-id';

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
            'bpchar' => ['type' => 'string', 'default' => ''],
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

    public static function isPgsql(Builder $query): bool {
        return $query->getConnection()->getDriverName() === 'pgsql';
    }

    /**
     * Introspecção das colunas da tabela do model, já normalizada para o formato
     * produzido por getColumnsFromTable(). Compartilhada por filtros, ordenação e
     * busca textual para evitar múltiplas chamadas ao schema na mesma requisição.
     */
    public static function getColumnsCollection(Builder $query): Collection {
        return collect(self::getColumnsFromTable($query->getModel()->getTable()));
    }

    /**
     * Normaliza as opções de busca (normalização de caixa/acento e máscara),
     * resolvendo o driver do banco uma única vez por requisição.
     */
    private static function resolveSearchOptions(Builder $query, array $options): array {
        return [
            'is_pgsql' => $options['is_pgsql'] ?? self::isPgsql($query),
            'masked_columns' => $options['masked_columns'] ?? [],
            'not_normalized_columns' => $options['not_normalized_columns'] ?? [],
        ];
    }

    private static function isNormalizedStringColumn(string $columnName, string $type, array $searchOptions): bool {
        return $type === 'string'
            && $searchOptions['is_pgsql']
            && !in_array($columnName, $searchOptions['not_normalized_columns'], true);
    }

    /**
     * Busca textual multi-coluna (`q`) sobre as colunas informadas, combinando-as
     * com OR em um único grupo. Colunas ausentes do schema são ignoradas, assim
     * como acontece em filtros e ordenações.
     *
     * Aceita tanto a lista de nomes (`['id', 'name']`) quanto o mapa legado
     * (`['id' => 'int']`).
     */
    public static function setSearchOnQuery(Builder $query, mixed $term, array $columnsToSearch = [], array $options = []): Builder {
        $term = match (true) {
            $term === null => '',
            is_bool($term) => $term ? '1' : '0',
            is_scalar($term), $term instanceof Stringable => trim((string) $term),
            default => '',
        };

        if ($term === '' || count($columnsToSearch) === 0) {
            return $query;
        }

        $searchOptions = self::resolveSearchOptions($query, $options);
        $schemaColumns = $options['columns'] ?? self::getColumnsCollection($query);

        $columns = [];

        foreach ($columnsToSearch as $key => $value) {
            $columnName = is_int($key) ? $value : $key;
            $column = $schemaColumns->firstWhere('name', '=', $columnName);

            if ($column) {
                $columns[] = $column;
            }
        }

        if (count($columns) === 0) {
            return $query;
        }

        $query->where(function (Builder $nestedQuery) use ($columns, $term, $searchOptions) {
            foreach ($columns as $column) {
                self::applySearchCondition($nestedQuery, $column, $term, $searchOptions);
            }

            return $nestedQuery;
        });

        return $query;
    }

    private static function applySearchCondition(Builder $query, array $column, string $term, array $searchOptions): void {
        $columnName = $column['name'];
        $type = $column['type'];

        if (self::isNormalizedStringColumn($columnName, $type, $searchOptions)) {
            self::addStringSearchToWhere(
                $query,
                $columnName,
                SqlQueryOperatorsEnum::Like->value,
                $term,
                in_array($columnName, $searchOptions['masked_columns'], true),
                'or',
            );

            return;
        }

        match ($type) {
            'string' => $query->orWhere($columnName, SqlQueryOperatorsEnum::Like->value, "%{$term}%"),
            'int' => $query->orWhere($columnName, SqlQueryOperatorsEnum::Equal->value, (int) $term),
            'bool' => $query->orWhere($columnName, SqlQueryOperatorsEnum::Equal->value, (bool) $term),
            'Carbon' => self::applyDateSearchCondition($query, $columnName, $term),
            default => $query->orWhere($columnName, SqlQueryOperatorsEnum::Equal->value, $term),
        };
    }

    /**
     * `q` é texto livre: um valor que não seja data não pode derrubar a busca
     * inteira, então a coluna de data é apenas ignorada.
     */
    private static function applyDateSearchCondition(Builder $query, string $columnName, string $term): void {
        try {
            $date = Carbon::parse($term);
        } catch (Throwable) {
            return;
        }

        $query->orWhere($columnName, SqlQueryOperatorsEnum::Equal->value, $date);
    }

    public static function setFiltersOnQuery(Builder $query, array $filters = [], array $columnsToFilter = [], array $options = []): Builder {
        $columns = $options['columns'] ?? self::getColumnsCollection($query);

        $searchOptions = self::resolveSearchOptions($query, $options);

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

                if (self::isNormalizedStringColumn($columnName, $column['type'], $searchOptions)) {
                    $isMasked = in_array($columnName, $searchOptions['masked_columns'], true);

                    self::addStringSearchToWhere(
                        $query,
                        $column['name'],
                        $sqlOperatorEnum->value,
                        $value,
                        $isMasked,
                    );

                    continue;
                }

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

    public static function addStringSearchToWhere(Builder $query, string $columnName, string $sqlOperator, mixed $value, bool $masked = false, string $boolean = 'and'): void {
        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($columnName);

        $columnExpression = $masked ? "REGEXP_REPLACE($wrappedColumn, '[^[:alnum:]]', '', 'g')": $wrappedColumn;

        $columnExpression = "unaccent(LOWER($columnExpression))";
        $valueExpression = $masked ? "REGEXP_REPLACE(?, '[^[:alnum:]]', '', 'g')" : '?';
        $valueExpression = "unaccent(LOWER($valueExpression))";

        $sql = match ($sqlOperator) {
            SqlQueryOperatorsEnum::Like->value => "$columnExpression LIKE '%' || $valueExpression || '%'",
            SqlQueryOperatorsEnum::Equal->value => "$columnExpression = $valueExpression",
            SqlQueryOperatorsEnum::NotEqual->value => "$columnExpression <> $valueExpression",
            default => "$columnExpression $sqlOperator $valueExpression",
        };

        $query->whereRaw($sql, [$value], $boolean);
    }

    /**
     * Aplica ordenação múltipla por CSV, com `-` indicando decrescente. Coluna
     * inexistente é ignorada. Quando $options['columns'] é informado, reaproveita a
     * introspecção já feita na mesma requisição.
     */
    public static function setSortsOnQuery(Builder $query, string $sortOptions, array $options = []): Builder {
        if (empty($sortOptions)) {
            return $query;
        }

        $sorts = explode(',', $sortOptions);

        $columns = $options['columns'] ?? self::getColumnsCollection($query);

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