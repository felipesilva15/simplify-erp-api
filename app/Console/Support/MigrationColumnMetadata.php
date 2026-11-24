<?php

namespace App\Console\Support;

use Illuminate\Support\Facades\File;

class MigrationColumnMetadata
{
    private const DEFAULT_STRING_LENGTH = 255;

    private const DEFAULT_DECIMAL_TOTAL = 12;

    private const DEFAULT_DECIMAL_SCALE = 2;

    /**
     * Completa `max_length` e `precision` quando o driver não informa o tamanho
     * da coluna (SQLite, por exemplo), usando as migrations da tabela alvo.
     *
     * @param  array<int, array<string, mixed>>  $fields
     */
    public static function applyToFields(string $tableName, array &$fields): void
    {
        $metadata = self::fromMigrations($tableName);

        foreach ($fields as &$field) {
            $column = $field['name'];

            if ($field['type'] === 'float') {
                $decimal = $metadata[$column]['decimal'] ?? null;
                $scale = $decimal['scale'] ?? ($field['precision'] ?: self::DEFAULT_DECIMAL_SCALE);
                $total = $decimal['total'] ?? ($field['max_length'] ?: self::DEFAULT_DECIMAL_TOTAL);

                if ($scale >= $total) {
                    $total = $scale + 1;
                }

                $field['max_length'] = $total;
                $field['precision'] = $scale;

                continue;
            }

            if ($field['type'] === 'string' && ! $field['max_length']) {
                $field['max_length'] = $metadata[$column]['length'] ?? self::DEFAULT_STRING_LENGTH;
            }
        }

        unset($field);
    }

    /**
     * @return array<string, array<string, array<string, int>>>
     */
    private static function fromMigrations(string $tableName): array
    {
        $metadata = [];
        $tablePattern = '/Schema::create\(\s*[\'"]'.preg_quote($tableName, '/').'[\'"]/';

        foreach (File::glob(database_path('migrations/*.php')) as $path) {
            $content = File::get($path);

            if (! preg_match($tablePattern, $content)) {
                continue;
            }

            if (preg_match_all(
                '/\$table->(?:decimal|unsignedDecimal|numeric)\(\s*([\'"])([^\'"]+)\1\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/',
                $content,
                $matches,
                PREG_SET_ORDER,
            )) {
                foreach ($matches as $match) {
                    $metadata[$match[2]]['decimal'] = [
                        'total' => (int) $match[3],
                        'scale' => (int) $match[4],
                    ];
                }
            }

            if (preg_match_all(
                '/\$table->(?:string|char)\(\s*([\'"])([^\'"]+)\1\s*,\s*(\d+)\s*\)/',
                $content,
                $matches,
                PREG_SET_ORDER,
            )) {
                foreach ($matches as $match) {
                    $metadata[$match[2]]['length'] = (int) $match[3];
                }
            }
        }

        return $metadata;
    }
}
