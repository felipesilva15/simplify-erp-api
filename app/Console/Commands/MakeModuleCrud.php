<?php

namespace App\Console\Commands;

use App\Core\Helpers\ModelHelpers;
use App\Core\Helpers\StringHelpers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCrud extends Command
{
    private const SPACES_PER_TAB = 4;

    protected $signature = 'make:module-crud 
                            {module : Name of module} 
                            {entity : Name of entity} 
                            {--all : Whether all files should be created} 
                            {--model : Whether the model should be created} 
                            {--service : Whether the service should be created} 
                            {--repository : Whether the repository should be created} 
                            {--dto : Whether the DTO should be created} 
                            {--controller : Whether the controller should be created} 
                            {--request : Whether the requests should be created} 
                            {--resource : Whether the resources should be created} 
                            {--lookup : Whether the lookup resources should be created} 
                            {--policy : Whether the policy should be created} 
                            {--factory : Whether the factory should be created} 
                            {--export : Whether the Excel export should be created} 
                            {--test : Whether the tests should be created}';

    protected $description = 'Create a new domain module files (Repository, Service, DTO, etc)';

    private string $module = '';

    private string $entity = '';

    private string $rootPath = '';

    private string $stubPath = '';

    private array $entityFields = [];

    private array $commonFields = ['id', 'created_at', 'updated_at', 'deleted_at'];

    private array $folders = [
        '{{root_path}}',
        '{{root_path}}/Models',
        '{{root_path}}/Services',
        '{{root_path}}/Exports',
        '{{root_path}}/Repositories/Eloquent',
        '{{root_path}}/Repositories/Interfaces',
        '{{root_path}}/DTO',
        '{{root_path}}/Http/Controllers',
        '{{root_path}}/Http/Requests/{{entity}}',
        '{{root_path}}/Http/Resources/{{entity}}',
        '{{app_path}}/Policies',
        '{{database_path}}/factories',
        '{{tests_path}}/Feature/{{module}}',
    ];

    private array $files = [
        'model' => [
            'option' => 'model',
            'stub' => 'module.model.stub',
            'path' => '{{root_path}}/Models/{{entity}}.php',
            'replacements' => 'getModelReplacements',
        ],
        'policy' => [
            'option' => 'policy',
            'stub' => 'module.policy.stub',
            'path' => '{{app_path}}/Policies/{{entity}}Policy.php',
            'replacements' => 'getPolicyReplacements',
        ],
        'factory' => [
            'option' => 'factory',
            'stub' => 'module.factory.stub',
            'path' => '{{database_path}}/factories/{{entity}}Factory.php',
            'replacements' => 'getFactoryReplacements',
        ],
        'test' => [
            'option' => 'test',
            'stub' => 'module.test.stub',
            'path' => '{{tests_path}}/Feature/{{module}}/{{entity}}Test.php',
            'replacements' => 'getTestReplacements',
        ],
        'service' => [
            'option' => 'service',
            'stub' => 'module.service.stub',
            'path' => '{{root_path}}/Services/{{entity}}Service.php',
        ],
        'repository' => [
            'option' => 'repository',
            'stub' => 'module.repository.stub',
            'path' => '{{root_path}}/Repositories/Eloquent/{{entity}}Repository.php',
            'replacements' => 'getRepositoryReplacements',
        ],
        'repositoryInterface' => [
            'option' => 'repository',
            'stub' => 'module.repository-interface.stub',
            'path' => '{{root_path}}/Repositories/Interfaces/{{entity}}RepositoryInterface.php',
        ],
        'dto' => [
            'option' => 'dto',
            'stub' => 'module.dto.stub',
            'path' => '{{root_path}}/DTO/{{entity}}DTO.php',
            'replacements' => 'getDtoReplacements',
        ],
        'controller' => [
            'option' => 'controller',
            'stub' => 'module.controller.stub',
            'path' => '{{root_path}}/Http/Controllers/{{entity}}Controller.php',
            'replacements' => 'getControllerReplacements',
        ],
        'storeRequest' => [
            'option' => 'request',
            'stub' => 'module.request-store.stub',
            'path' => '{{root_path}}/Http/Requests/{{entity}}/Store{{entity}}Request.php',
            'replacements' => 'getRequestReplacements',
        ],
        'updateRequest' => [
            'option' => 'request',
            'stub' => 'module.request-update.stub',
            'path' => '{{root_path}}/Http/Requests/{{entity}}/Update{{entity}}Request.php',
            'replacements' => 'getRequestReplacements',
        ],
        'resource' => [
            'option' => 'resource',
            'stub' => 'module.resource.stub',
            'path' => '{{root_path}}/Http/Resources/{{entity}}/{{entity}}Resource.php',
            'replacements' => 'getResourceReplacements',
        ],
        'collection' => [
            'option' => 'resource',
            'stub' => 'module.resource-collection.stub',
            'path' => '{{root_path}}/Http/Resources/{{entity}}/{{entity}}Collection.php',
        ],
        'resourceLookup' => [
            'option' => 'lookup',
            'stub' => 'module.resource-lookup.stub',
            'path' => '{{root_path}}/Http/Resources/{{entity}}/{{entity}}LookupResource.php',
            'replacements' => 'getLookupReplacements',
        ],
        'lookupCollection' => [
            'option' => 'lookup',
            'stub' => 'module.resource-lookup-collection.stub',
            'path' => '{{root_path}}/Http/Resources/{{entity}}/{{entity}}LookupCollection.php',
        ],
        'export' => [
            'option' => 'export',
            'stub' => 'module.export.stub',
            'path' => '{{root_path}}/Exports/{{entity}}Export.php',
            'replacements' => 'getExportReplacements',
        ],
    ];

    private array $requestTypes = [
        'string' => 'string',
        'float' => 'decimal',
        'int' => 'integer',
        'Carbon' => 'date',
        'bool' => 'boolean',
    ];

    private array $swaggerTypes;

    public function __construct()
    {
        parent::__construct();

        $this->swaggerTypes = [
            'string' => ['type' => 'string', 'format' => '', 'example' => '"Sample"'],
            'float' => ['type' => 'number', 'format' => 'float', 'example' => '20.99'],
            'int' => ['type' => 'integer', 'format' => '', 'example' => '1'],
            'Carbon' => ['type' => 'string', 'format' => 'date-time', 'example' => '"'.Carbon::now()->toISOString().'"'],
            'bool' => ['type' => 'boolean', 'format' => '', 'example' => 'false'],
        ];
    }

    public function handle(): int
    {
        $this->module = Str::studly($this->argument('module'));
        $this->entity = Str::studly($this->argument('entity'));
        $this->rootPath = app_path("Modules/{$this->module}");
        $this->stubPath = app_path('Console/Stubs');

        $this->makeFolders();
        $this->loadEntityFields();

        $generatedOptions = [];

        foreach ($this->files as $config) {
            if (! $this->shouldGenerate($config['option'])) {
                continue;
            }

            $this->createFile($config);
            $generatedOptions[$config['option']] = true;
        }

        if (isset($generatedOptions['repository'])) {
            $this->updateModuleProvider();
        }

        if (isset($generatedOptions['controller'])) {
            $this->registerModuleProvider();
            $this->updateModuleRoutes();
            $this->updateSwaggerTagGroups();
        }

        $this->info("Module [{$this->rootPath}] created successfully for {$this->entity} Entity!");

        return self::SUCCESS;
    }

    private function shouldGenerate(string $option): bool
    {
        // Lookup e export são opt-in: não são implicados por --all
        if (in_array($option, ['lookup', 'export'], true)) {
            return (bool) $this->option($option);
        }

        return $this->option('all') || $this->option($option);
    }

    private function createFile(array $config): void
    {
        $stub = File::get("{$this->stubPath}/{$config['stub']}");
        $content = $this->replacePlaceholders($stub, $this->getReplacements($config));

        File::put($this->replacePlaceholders($config['path'], $this->getBaseReplacements()), $content);
    }

    private function makeFolders(): void
    {
        foreach ($this->folders as $folder) {
            $path = $this->replacePlaceholders($folder, $this->getBaseReplacements());

            if (! File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }
    }

    private function getReplacements(array $config): array
    {
        $replacements = $this->getBaseReplacements();

        if (isset($config['replacements'])) {
            $replacements = array_merge($replacements, $this->{$config['replacements']}());
        }

        return $replacements;
    }

    private function getBaseReplacements(): array
    {
        $pluralEntity = Str::pluralStudly($this->entity);

        return [
            '{{root_path}}' => $this->rootPath,
            '{{app_path}}' => app_path(),
            '{{database_path}}' => database_path(),
            '{{tests_path}}' => base_path('tests'),
            '{{module}}' => $this->module,
            '{{entity}}' => $this->entity,
            '{{lower_module}}' => Str::kebab($this->module),
            '{{lower_entity}}' => Str::lower(Str::snake($this->entity)),
            '{{route_entity}}' => Str::kebab($pluralEntity),
            '{{human_entity}}' => Str::lower(Str::headline($this->entity)),
            '{{human_entities}}' => Str::lower(Str::headline($pluralEntity)),
            '{{permission_entity}}' => Str::camel($pluralEntity),
            '{{table_name}}' => $this->getTableNameByEntityName($this->entity),
        ];
    }

    private function replacePlaceholders(string $content, array $replacements): string
    {
        return strtr($content, $replacements);
    }

    private function readStubBlock(string $stub): string
    {
        return rtrim(File::get("{$this->stubPath}/{$stub}"));
    }

    private function tabs(int $levels): string
    {
        return str_repeat(' ', $levels * self::SPACES_PER_TAB);
    }

    private function loadEntityFields(): void
    {
        $this->entityFields = ModelHelpers::getColumnsFromTable($this->getTableNameByEntityName($this->entity));
    }

    private function getTableNameByEntityName(string $entityName): string
    {
        $entityName = strtolower(Str::snake($entityName));

        if (str_ends_with($entityName, 's')) {
            return $entityName.'es';
        }

        if (str_ends_with($entityName, 'y')) {
            return substr($entityName, 0, -1).'ies';
        }

        return $entityName.'s';
    }

    private function getModelReplacements(): array
    {
        return [
            '{{fillable_fields}}' => $this->renderLines(
                array_map(fn ($field) => "'{$field['name']}'", $this->entityFields(skipCommon: true)),
                indent: $this->tabs(2),
            ),
            ...$this->getSwaggerFields(setCommonProperties: true, setValidationAttributes: false),
        ];
    }

    private function getDtoReplacements(): array
    {
        $properties = [];
        $params = [];
        $arrayFields = [];

        foreach ($this->entityFields() as $field) {
            $default = StringHelpers::toStringLiteral($field['default']);
            $arrayFields[] = "'{$field['name']}' => \$this->{$field['name']}";

            if ($field['type'] === 'Carbon') {
                $properties[] = "public ?Carbon \${$field['name']} = null";
                $params[] = "{$field['name']}: !empty(\$data['{$field['name']}']) ? Carbon::parse(\$data['{$field['name']}']) : null";

                continue;
            }

            $properties[] = 'public '.($field['nullable'] ? '?' : '').$field['type'].' $'.$field['name'].' = '.$default;
            $params[] = "{$field['name']}: \$data['{$field['name']}'] ?? {$default}";
        }

        return [
            '{{dto_carbon_import}}' => $this->hasCarbonFields() ? 'use Carbon\Carbon;' : '',
            '{{constructor_properties}}' => $this->renderLines($properties, indent: $this->tabs(2)),
            '{{constructor_params}}' => $this->renderLines($params, indent: $this->tabs(3)),
            '{{array_fields}}' => $this->renderLines($arrayFields, indent: $this->tabs(3)),
        ];
    }

    private function getControllerReplacements(): array
    {
        $base = $this->getBaseReplacements();

        return [
            '{{index_filter_parameters}}' => $this->renderIndexFilterParameters(),
            '{{lookup_imports}}' => $this->option('lookup')
                ? $this->replacePlaceholders($this->readStubBlock('module.controller-lookup-imports.stub'), $base).PHP_EOL
                : '',
            '{{lookup_method}}' => $this->option('lookup')
                ? $this->replacePlaceholders($this->readStubBlock('module.controller-lookup.stub'), $base)
                : '',
            '{{export_imports}}' => $this->option('export')
                ? PHP_EOL.$this->replacePlaceholders($this->readStubBlock('module.controller-export-imports.stub'), $base)
                : '',
            '{{export_trait}}' => $this->option('export')
                ? PHP_EOL.'    use HasExcelExport;'
                : '',
            '{{export_swagger}}' => $this->option('export')
                ? $this->replacePlaceholders($this->readStubBlock('module.controller-export-swagger.stub'), [
                    ...$base,
                    '{{export_filter_parameters}}' => $this->renderIndexFilterParameters(),
                ])
                : '',
            '{{export_methods}}' => $this->option('export')
                ? PHP_EOL.PHP_EOL.$this->tabs(1).$this->replacePlaceholders($this->readStubBlock('module.controller-export-methods.stub'), $base)
                : '',
        ];
    }

    private function getPolicyReplacements(): array
    {
        return [
            '{{export_policy_method}}' => $this->option('export')
                ? PHP_EOL.PHP_EOL.$this->tabs(1).$this->replacePlaceholders($this->readStubBlock('module.policy-export.stub'), $this->getBaseReplacements())
                : '',
        ];
    }

    private function getExportReplacements(): array
    {
        $headings = ["'ID'"];
        $mapFields = ['$row->id'];

        foreach ($this->entityFields(skipCommon: true) as $field) {
            $headings[] = "'".Str::headline($field['name'])."'";

            $mapFields[] = $field['type'] === 'Carbon'
                ? "\$row->{$field['name']}?->format('Y-m-d H:i:s')"
                : "\$row->{$field['name']}";
        }

        return [
            '{{export_headings}}' => $this->renderLines($headings, indent: $this->tabs(3)),
            '{{export_map_fields}}' => $this->renderLines($mapFields, indent: $this->tabs(3)),
        ];
    }

    private function getRequestReplacements(): array
    {
        return [
            '{{rules_definitions}}' => $this->renderLines(
                array_map(
                    fn ($field) => "'{$field['name']}' => '{$this->buildRule($field)}'",
                    $this->entityFields(skipCommon: true),
                ),
                indent: $this->tabs(3),
            ),
            ...$this->getSwaggerFields(setCommonProperties: false, setValidationAttributes: true),
        ];
    }

    private function getResourceReplacements(): array
    {
        return [
            '{{array_fields}}' => $this->renderLines(
                array_map(fn ($field) => "'{$field['name']}' => \$this->{$field['name']}", $this->entityFields()),
                indent: $this->tabs(3),
            ),
            ...$this->getSwaggerFields(setCommonProperties: true, setValidationAttributes: true),
        ];
    }

    private function getFactoryReplacements(): array
    {
        return [
            '{{factory_attributes}}' => $this->renderLines(
                array_map(
                    fn ($field) => "'{$field['name']}' => {$this->fakerExpression($field)}",
                    $this->entityFields(skipCommon: true),
                ),
                indent: $this->tabs(3),
            ),
        ];
    }

    private function getTestReplacements(): array
    {
        $fields = $this->entityFields(skipCommon: true);
        $stringField = array_values(array_filter($fields, fn ($field) => $field['type'] === 'string'))[0] ?? null;
        $requiredField = array_values(array_filter($fields, fn ($field) => ! $field['nullable']))[0] ?? null;

        $displayField = $stringField['name'] ?? ($fields[0]['name'] ?? '');
        $requiredField = $requiredField['name'] ?? $displayField;

        return [
            '{{resource_structure}}' => $this->renderLines(
                array_map(fn ($field) => "'{$field['name']}'", $this->entityFields()),
                indent: $this->tabs(3),
            ),
            '{{display_field}}' => $displayField,
            '{{required_field}}' => $requiredField,
        ];
    }

    private function getRepositoryReplacements(): array
    {
        $stringFields = array_values(array_filter(
            $this->entityFields(skipCommon: true),
            fn ($field) => $field['type'] === 'string',
        ));

        $columns = array_map(fn ($field) => "'{$field['name']}' => 'string'", $stringFields);

        if (count($columns) === 0) {
            $columns = ["'id' => 'int'"];
        }

        $lookupColumns = $this->renderLines($columns, indent: $this->tabs(3));
        $lookupKeyMethod = '';

        if ($this->option('lookup')) {
            $keyName = $this->getLookupKeyField()['name'] ?? 'id';

            if ($keyName !== 'id') {
                $lookupKeyMethod = $this->replacePlaceholders(
                    $this->readStubBlock('module.repository-lookup-key.stub'),
                    ['{{lookup_key_column}}' => $keyName],
                ).PHP_EOL.PHP_EOL;
            }
        }

        return [
            '{{lookup_columns}}' => $lookupColumns,
            '{{lookup_columns_method}}' => $this->option('lookup')
                ? $this->replacePlaceholders($this->readStubBlock('module.repository-lookup.stub'), ['{{lookup_columns}}' => $lookupColumns]).PHP_EOL.PHP_EOL
                : '',
            '{{lookup_key_method}}' => $lookupKeyMethod,
        ];
    }

    private function getLookupReplacements(): array
    {
        $fields = $this->entityFields(skipCommon: true);
        $stringFields = array_values(array_filter($fields, fn ($field) => $field['type'] === 'string'));

        $labelField = null;

        foreach ($stringFields as $field) {
            if ($field['name'] === 'name') {
                $labelField = $field;
                break;
            }
        }

        if ($labelField === null && count($stringFields) > 0) {
            $labelField = $stringFields[0];
        }

        $keyField = $this->getLookupKeyField();

        $usesIdAsKey = $keyField === null;
        $keyName = $usesIdAsKey ? 'id' : $keyField['name'];
        $labelName = $labelField['name'] ?? 'id';
        $labelMaxLength = $labelField['max_length'] ?? 80;

        $metaNames = ['id'];

        foreach ([$labelName, $keyName] as $name) {
            if (! in_array($name, $metaNames, true)) {
                $metaNames[] = $name;
            }
        }

        foreach ($stringFields as $field) {
            if (! in_array($field['name'], $metaNames, true)) {
                $metaNames[] = $field['name'];
            }

            if (count($metaNames) >= 5) {
                break;
            }
        }

        $allFields = $this->entityFields();
        $metaProperties = [];

        foreach ($metaNames as $name) {
            $metaField = null;

            foreach ($allFields as $field) {
                if ($field['name'] === $name) {
                    $metaField = $field;
                    break;
                }
            }

            $metaProperties[] = $this->getLookupMetaProperty(
                $metaField ?? ['name' => $name, 'type' => 'string', 'nullable' => false, 'max_length' => $labelMaxLength],
            );
        }

        return [
            '{{lookup_key_type}}' => $usesIdAsKey ? 'integer' : 'string',
            '{{lookup_key_example}}' => $usesIdAsKey ? '1' : '"Sample"',
            '{{lookup_label_max_length}}' => $labelMaxLength,
            '{{lookup_key_accessor}}' => $usesIdAsKey ? '$this->id' : '$this->'.$keyName,
            '{{lookup_label_accessor}}' => '$this->'.$labelName,
            '{{lookup_meta_args}}' => implode(', ', array_map(fn ($name) => "'".$name."'", $metaNames)),
            '{{lookup_meta_properties}}' => $this->renderLines($metaProperties, indent: ' * '.$this->tabs(2)),
        ];
    }

    private function getLookupKeyField(): ?array
    {
        foreach ($this->entityFields(skipCommon: true) as $field) {
            if ($field['type'] === 'string' && $field['name'] === 'code') {
                return $field;
            }
        }

        return null;
    }

    private function getLookupMetaProperty(array $field): string
    {
        if ($field['name'] === 'id') {
            return '@OA\Property(property="id", type="integer", example=1)';
        }

        return $this->getSwaggerProperty($field, true);
    }

    private function renderIndexFilterParameters(): string
    {
        $lines = [];

        foreach ($this->entityFields() as $field) {
            $schemaType = match ($field['type']) {
                'int' => 'integer',
                'float' => 'number',
                'bool' => 'boolean',
                'Carbon' => 'string',
                default => 'string',
            };

            foreach ($this->filterOperatorsForType($field['type']) as $operator) {
                $lines[] = '@OA\Parameter(name="filters['.$field['name'].']['.$operator.']", in="query", required=false, @OA\Schema(type="'.$schemaType.'"))';
            }
        }

        $rendered = '';
        $prefix = $this->tabs(1).' * '.$this->tabs(1);

        foreach ($lines as $index => $line) {
            $rendered .= ($index > 0 ? $prefix : '').$line.','.PHP_EOL;
        }

        return rtrim($rendered, PHP_EOL);
    }

    private function filterOperatorsForType(string $type): array
    {
        return match ($type) {
            'string' => ['eq', 'like', 'ne'],
            'bool' => ['eq', 'ne'],
            default => ['eq', 'lt', 'lte', 'gt', 'gte', 'ne'],
        };
    }

    private function fakerExpression(array $field): string
    {
        if (str_contains($field['name'], 'mail')) {
            return 'fake()->unique()->safeEmail()';
        }

        if (str_contains($field['name'], 'slug')) {
            return 'strtolower(fake()->name())';
        }

        if (str_contains($field['name'], 'phone')) {
            return "fake()->numerify('###########')";
        }

        return match ($field['type']) {
            'string' => 'fake()->name()',
            'int' => 'fake()->numberBetween(0, 100)',
            'float' => 'fake()->randomFloat(2, 0, 9999)',
            'bool' => 'fake()->boolean()',
            'Carbon' => "fake()->date('Y-m-d H:i:s')",
            default => 'fake()->name()',
        };
    }

    private function buildRule(array $field): string
    {
        $rules = [$field['nullable'] ? 'nullable' : 'required'];

        $typeRule = $this->requestTypes[$field['type']];
        $rules[] = $field['type'] === 'float' ? "{$typeRule}:{$field['precision']}" : $typeRule;

        if ($field['max_length'] && $field['type'] === 'string') {
            $rules[] = 'min:1';
            $rules[] = "max:{$field['max_length']}";
        }

        if (str_contains($field['name'], 'mail')) {
            $rules[] = 'email';
        }

        return implode('|', $rules);
    }

    private function getSwaggerFields(bool $setCommonProperties, bool $setValidationAttributes): array
    {
        $properties = [];
        $required = [];

        foreach ($this->entityFields(skipCommon: ! $setCommonProperties) as $field) {
            $properties[] = $this->getSwaggerProperty($field, $setValidationAttributes);

            if ($setValidationAttributes && ! $field['nullable']) {
                $required[] = '"'.$field['name'].'"';
            }
        }

        return [
            '{{swagger_properties}}' => $this->renderLines($properties, indent: ' * '.$this->tabs(1)),
            '{{swagger_required}}' => implode(',', $required),
        ];
    }

    private function getSwaggerProperty(array $field, bool $setValidationAttributes): string
    {
        $type = $this->swaggerTypes[$field['type']];
        $property = "@OA\Property(property=\"{$field['name']}\", type=\"{$type['type']}\"";

        if ($type['format']) {
            $property .= ", format=\"{$type['format']}\"";
        }

        $property .= ", example={$type['example']}";

        if ($setValidationAttributes) {
            switch ($field['type']) {
                case 'string':
                    $property .= ', minLength=1, maxLength='.$field['max_length'];
                    break;

                case 'float':
                    $property .= ', minimum=0.'.str_pad('', $field['precision'] - 1, '0').'1';
                    $property .= ', maximum='.str_pad('', $field['max_length'] - $field['precision'], '9').'.'.str_pad('', $field['precision'], '9');
                    break;
            }
        }

        if ($field['nullable']) {
            $property .= ', nullable=true';
        }

        return $property.')';
    }

    private function entityFields(bool $skipCommon = false): array
    {
        if (! $skipCommon) {
            return $this->entityFields;
        }

        return array_values(array_filter(
            $this->entityFields,
            fn ($field) => ! in_array($field['name'], $this->commonFields),
        ));
    }

    private function hasCarbonFields(): bool
    {
        foreach ($this->entityFields as $field) {
            if ($field['type'] === 'Carbon') {
                return true;
            }
        }

        return false;
    }

    private function renderLines(array $lines, string $indent, bool $trailingComma = true): string
    {
        $output = '';
        $lastIndex = count($lines) - 1;

        foreach ($lines as $index => $line) {
            $output .= $index > 0 ? $indent : '';
            $output .= $line;
            $output .= $index < $lastIndex && $trailingComma ? ',' : '';
            $output .= PHP_EOL;
        }

        return rtrim($output, PHP_EOL);
    }

    private function updateModuleProvider(): void
    {
        $path = app_path("Providers/{$this->module}ModuleProvider.php");
        $repository = "App\\Modules\\{$this->module}\\Repositories\\Eloquent\\{$this->entity}Repository";
        $repositoryInterface = "App\\Modules\\{$this->module}\\Repositories\\Interfaces\\{$this->entity}RepositoryInterface";
        $bind = $this->tabs(2)."\$this->app->bind({$this->entity}RepositoryInterface::class, {$this->entity}Repository::class);";

        if (! File::exists($path)) {
            $content = $this->replacePlaceholders(File::get("{$this->stubPath}/module.provider.stub"), [
                ...$this->getBaseReplacements(),
                '{{repository_class}}' => $repository,
                '{{repository_interface_class}}' => $repositoryInterface,
                '{{binding}}' => $bind,
            ]);

            File::put($path, $content);

            return;
        }

        $content = File::get($path);

        if (str_contains($content, $bind)) {
            return;
        }

        $content = str_replace(
            "use Illuminate\Support\ServiceProvider;",
            "use {$repository};\nuse {$repositoryInterface};\nuse Illuminate\Support\ServiceProvider;",
            $content,
        );

        $content = str_replace("\n".$this->tabs(1)."}\n}", "\n{$bind}\n".$this->tabs(1)."}\n}", $content, $count);

        if ($count > 0) {
            File::put($path, $content);
        }
    }

    private function registerModuleProvider(): void
    {
        $path = base_path('bootstrap/providers.php');
        $content = File::get($path);
        $provider = "App\\Providers\\{$this->module}ModuleProvider";

        if (str_contains($content, "{$provider}::class")) {
            return;
        }

        if (! str_contains($content, "{$provider}::class")) {
            $content = str_replace('];', $this->tabs(1)."{$provider}::class,\n];", $content);
        }

        File::put($path, $content);
    }

    private function updateModuleRoutes(): void
    {
        $path = base_path('routes/api.php');
        $content = File::get($path);
        $kebabModule = Str::kebab($this->module);
        $routeEntity = Str::kebab(Str::pluralStudly($this->entity));

        if (str_contains($content, "Route::crudResource('{$routeEntity}',")) {
            return;
        }

        $controllerImport = "use App\\Modules\\{$this->module}\\Http\\Controllers\\{$this->entity}Controller;";

        if (! str_contains($content, $controllerImport)) {
            $content = str_replace(
                "use Illuminate\Support\Facades\Route;",
                "{$controllerImport}\nuse Illuminate\Support\Facades\Route;",
                $content,
            );
        }

        $prefixOpen = "Route::prefix('{$kebabModule}')->group(function() {";

        if (str_contains($content, $prefixOpen)) {
            $end = $this->findGroupEnd($content, $prefixOpen);

            if ($end === null) {
                return;
            }

            $lineStart = strrpos(substr($content, 0, $end), "\n") + 1;
            $routes = $this->getLookupRouteLine($routeEntity).$this->getExportRouteLine($routeEntity).$this->getCrudRouteLine($routeEntity);

            $content = substr($content, 0, $lineStart)."\n".$routes.substr($content, $lineStart);
        } else {
            $group = $this->buildModuleRouteGroup($routeEntity);
            $authOpen = "Route::group(['middleware' => 'auth'], function () {";
            $end = $this->findGroupEnd($content, $authOpen);

            if ($end === null) {
                return;
            }

            $lineStart = strrpos(substr($content, 0, $end), "\n") + 1;

            $content = substr($content, 0, $lineStart)."\n".$group."\n".substr($content, $lineStart);
        }

        File::put($path, $content);
    }

    private function buildModuleRouteGroup(string $routeEntity): string
    {
        $stub = File::get("{$this->stubPath}/module.route-group.stub");

        return $this->replacePlaceholders($stub, [
            ...$this->getBaseReplacements(),
            '{{lookup_route_line}}' => $this->getLookupRouteLine($routeEntity),
            '{{export_route_line}}' => $this->getExportRouteLine($routeEntity),
        ]);
    }

    private function getLookupRouteLine(string $routeEntity): string
    {
        if (! $this->option('lookup')) {
            return '';
        }

        return $this->tabs(2)."Route::get('{$routeEntity}/lookup', [{$this->entity}Controller::class, 'lookup'])->name('{$routeEntity}.lookup');".PHP_EOL;
    }

    private function getExportRouteLine(string $routeEntity): string
    {
        if (! $this->option('export')) {
            return '';
        }

        return $this->tabs(2)."Route::get('{$routeEntity}/export', [{$this->entity}Controller::class, 'export'])->name('{$routeEntity}.export');".PHP_EOL
            .$this->tabs(2)."Route::get('{$routeEntity}/export/{exportType}', [{$this->entity}Controller::class, 'export'])->name('{$routeEntity}.export.custom');".PHP_EOL;
    }

    private function getCrudRouteLine(string $routeEntity): string
    {
        return $this->tabs(2)."Route::crudResource('{$routeEntity}', {$this->entity}Controller::class);".PHP_EOL;
    }

    private function findGroupEnd(string $content, string $openNeedle): ?int
    {
        $openPosition = strpos($content, $openNeedle);

        if ($openPosition === false) {
            return null;
        }

        $start = (int) strpos($content, '{', $openPosition);
        $depth = 0;
        $length = strlen($content);

        for ($i = $start; $i < $length; $i++) {
            $character = $content[$i];

            if ($character === '{') {
                $depth++;
            }

            if ($character === '}') {
                $depth--;

                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    private function updateSwaggerTagGroups(): void
    {
        $path = app_path('Core/Http/Controllers/Controller.php');
        $content = File::get($path);
        $moduleTag = '"name"="'.$this->module.'"';

        if (str_contains($content, $moduleTag)) {
            $modulePosition = strpos($content, $moduleTag);
            $tagsPosition = strpos($content, '"tags"={', $modulePosition);

            if ($tagsPosition === false) {
                return;
            }

            $tagsEnd = strpos($content, '}', $tagsPosition);

            if ($tagsEnd === false) {
                return;
            }

            $tags = substr($content, $tagsPosition, $tagsEnd - $tagsPosition + 1);

            if (str_contains($tags, '"'.$this->entity.'"')) {
                return;
            }

            $newTags = substr_replace($tags, ', "'.$this->entity.'"', -1, 0);

            $content = substr_replace($content, $newTags, $tagsPosition, strlen($tags));
        } else {
            if (str_contains($content, '"'.$this->entity.'"')) {
                return;
            }

            $prefix = ' * ';
            $needle = $prefix.$this->tabs(3)."}\n".$prefix.$this->tabs(2)."}\n".$prefix.$this->tabs(1).'}';
            $replacement = $prefix.$this->tabs(3)."},\n"
                .$prefix.$this->tabs(3)."{\n"
                .$prefix.$this->tabs(4)."\"name\"=\"{$this->module}\",\n"
                .$prefix.$this->tabs(4)."\"tags\"={\"{$this->entity}\"}\n"
                .$prefix.$this->tabs(3)."}\n"
                .$prefix.$this->tabs(2)."}\n"
                .$prefix.$this->tabs(1).'}';

            $content = str_replace($needle, $replacement, $content, $count);

            if ($count === 0) {
                return;
            }
        }

        File::put($path, $content);
    }
}