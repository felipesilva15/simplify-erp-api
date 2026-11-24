<?php

namespace App\Console\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EnumResolver
{
    private const MIN_TOKEN_PREFIX = 5;

    private const GENERIC_COLUMN_TOKENS = ['code', 'id', 'name', 'type', 'value'];

    private array $enums = [];

    public function __construct(
        private string $module,
        private string $entity,
    ) {
        $this->loadEnums();
    }

    /**
     * Resolve enums apenas para as colunas informadas. O retorno é indexado pelo
     * nome da coluna e contém `class` (nome curto ou alias), `fqcn` e `schema`.
     *
     * @param  array<int, string>  $columns
     * @return array<string, array<string, string>|null>
     */
    public function resolveForColumns(array $columns): array
    {
        $resolved = [];

        foreach ($columns as $column) {
            $resolved[$column] = $this->findBest($column);
        }

        $aliases = $this->buildAliases($resolved);

        foreach ($resolved as $column => $enum) {
            if ($enum !== null) {
                $resolved[$column]['class'] = $aliases[$enum['fqcn']];
            }
        }

        return $resolved;
    }

    /**
     * @param  array<string, array<string, string>|null>  $resolved
     */
    public function importsFor(array $resolved): string
    {
        $lines = [];
        $seen = [];

        foreach ($resolved as $enum) {
            if ($enum === null || isset($seen[$enum['fqcn']])) {
                continue;
            }

            $seen[$enum['fqcn']] = true;
            $line = "use {$enum['fqcn']}";

            if ($enum['class'] !== $this->shortName($enum['fqcn'])) {
                $line .= " as {$enum['class']}";
            }

            $lines[] = $line.';';
        }

        return $lines === [] ? '' : implode(PHP_EOL, $lines).PHP_EOL;
    }

    private function loadEnums(): void
    {
        foreach (File::allFiles(app_path()) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());

            if (! preg_match('/^\s*(?:final\s+)?enum\s+([A-Za-z_]\w*)/m', $content, $nameMatch)) {
                continue;
            }

            if (! preg_match('/^\s*namespace\s+([^;]+);/m', $content, $namespaceMatch)) {
                continue;
            }

            $shortName = $nameMatch[1];
            $namespace = trim($namespaceMatch[1]);

            $this->enums[] = [
                'fqcn' => $namespace.'\\'.$shortName,
                'shortName' => $shortName,
                'namespace' => $namespace,
                'tokens' => $this->tokens($shortName),
            ];
        }
    }

    /**
     * @return array<string, string>|null
     */
    private function findBest(string $column): ?array
    {
        $columnTokens = $this->tokens($column);
        $meaningfulTokens = array_values(array_diff($columnTokens, self::GENERIC_COLUMN_TOKENS));
        $entityTokens = $this->tokens($this->entity);
        $contextTokens = array_values(array_unique(array_merge(
            $entityTokens,
            $this->tokens($this->module),
        )));

        $best = null;
        $bestScore = 0;

        foreach ($this->enums as $enum) {
            $matchesColumn = true;

            foreach ($meaningfulTokens as $token) {
                if (! $this->hasMatchingToken($token, $enum['tokens'])) {
                    $matchesColumn = false;
                    break;
                }
            }

            if (! $matchesColumn) {
                continue;
            }

            $isExactName = $this->isExactName($column, $enum['shortName']);

            if ($meaningfulTokens === [] && ! $isExactName) {
                continue;
            }

            $score = count($meaningfulTokens) * 10;

            foreach ($contextTokens as $token) {
                if ($this->hasMatchingToken($token, $enum['tokens'])) {
                    $score += in_array($token, $entityTokens, true) ? 6 : 3;
                }
            }

            if ($this->isExactName($column, $enum['shortName'])) {
                $score += 40;
            }

            $score += $this->namespaceScore($enum['namespace']);

            if ($score < 25 || $score <= $bestScore) {
                continue;
            }

            $best = $enum;
            $bestScore = $score;
        }

        if ($best === null) {
            return null;
        }

        return [
            'fqcn' => $best['fqcn'],
            'schema' => $best['shortName'],
        ];
    }

    private function isExactName(string $column, string $enumName): bool
    {
        $column = Str::studly($column);
        $entity = Str::studly($this->entity);
        $module = Str::studly($this->module);

        $candidates = [
            $column.'Enum',
            $column,
            $entity.$column.'Enum',
            $entity.$column,
            $module.$column.'Enum',
            $module.$column,
            $module.$entity.$column.'Enum',
            $module.$entity.$column,
        ];

        return in_array($enumName, $candidates, true);
    }

    private function namespaceScore(string $namespace): int
    {
        if (str_starts_with($namespace, 'App\\Modules\\')) {
            $module = Str::studly(Str::before(Str::after($namespace, 'App\\Modules\\'), '\\'));

            return $module === Str::studly($this->module) ? 30 : 0;
        }

        return str_starts_with($namespace, 'App\\Core') ? 15 : 0;
    }

    /**
     * @param  array<string, array<string, string>|null>  $resolved
     * @return array<string, string>
     */
    private function buildAliases(array $resolved): array
    {
        $fqcns = [];

        foreach ($resolved as $enum) {
            if ($enum !== null) {
                $fqcns[$enum['fqcn']] = true;
            }
        }

        $fqcns = array_keys($fqcns);
        $shortNameCounts = array_count_values(array_map(fn (string $fqcn) => $this->shortName($fqcn), $fqcns));
        $aliases = [];

        foreach ($fqcns as $fqcn) {
            $shortName = $this->shortName($fqcn);
            $alias = $shortName;

            if ($shortNameCounts[$shortName] > 1) {
                $parts = explode('\\', $fqcn);
                $origin = Str::studly($parts[2] ?? $parts[1] ?? 'Generated');
                $alias = $shortName.'From'.$origin;
            }

            while (in_array($alias, $aliases, true)) {
                $alias .= 'Alt';
            }

            $aliases[$fqcn] = $alias;
        }

        return $aliases;
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $value): array
    {
        return array_values(array_filter(
            explode('_', Str::snake($value)),
            fn (string $token) => $token !== '' && $token !== 'enum',
        ));
    }

    /**
     * @param  array<int, string>  $candidateTokens
     */
    private function hasMatchingToken(string $token, array $candidateTokens): bool
    {
        foreach ($candidateTokens as $candidate) {
            if ($token === $candidate) {
                return true;
            }

            $limit = min(strlen($token), strlen($candidate));
            $commonPrefix = 0;

            while ($commonPrefix < $limit && $token[$commonPrefix] === $candidate[$commonPrefix]) {
                $commonPrefix++;
            }

            if ($commonPrefix >= self::MIN_TOKEN_PREFIX) {
                return true;
            }
        }

        return false;
    }

    private function shortName(string $fqcn): string
    {
        return Str::afterLast($fqcn, '\\');
    }
}
