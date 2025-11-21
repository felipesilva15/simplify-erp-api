<?php

namespace App\Core\Helpers;

use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\Schema;

class ModelHelpers
{
    public static function getTableColumnsFromTable(string $tableName): array {
        $databaseFields = Schema::getColumns($tableName);
        $fields = [];

        $typesMap = [
            'varchar' => ['type' => 'string', 'default' => ''],
            'char' => ['type' => 'string', 'default' => ''],
            'text' => ['type' => 'string', 'default' => ''],
            'blob' => ['type' => 'string', 'default' => ''],
            'int' => ['type' => 'int', 'default' => 0],
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
}