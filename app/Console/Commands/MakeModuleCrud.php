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
                            {--policy : Whether the policy should be created} 
                            {--factory : Whether the factory should be created} 
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
    ];

    private array $requestTypes = [
        'string' => 'string',
        'float' => 'decimal',
        'int' => 'integer',
        'Carbon' => 'datetime',
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

        foreach ($this->files as $config) {
            if ($this->option('all') || $this->option($config['option'])) {
                $this->createFile($config);
            }
        }

        $this->info("Module [{$this->rootPath}] created successfully for {$this->entity} Entity!");

        return self::SUCCESS;
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
        return [
            '{{root_path}}' => $this->rootPath,
            '{{app_path}}' => app_path(),
            '{{database_path}}' => database_path(),
            '{{tests_path}}' => base_path('tests'),
            '{{module}}' => $this->module,
            '{{entity}}' => $this->entity,
            '{{lower_module}}' => strtolower($this->module),
            '{{lower_entity}}' => strtolower($this->entity),
            '{{table_name}}' => $this->getTableNameByEntityName($this->entity),
        ];
    }

    private function replacePlaceholders(string $content, array $replacements): string
    {
        return strtr($content, $replacements);
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
                indent: '        ',
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
            $properties[] = 'public '.($field['nullable'] ? '?' : '').$field['type'].' $'.$field['name'].' = '.$default;
            $params[] = "{$field['name']}: \$data['{$field['name']}'] ?? {$default}";
            $arrayFields[] = "'{$field['name']}' => \$this->{$field['name']}";
        }

        return [
            '{{constructor_properties}}' => $this->renderLines($properties, indent: '        '),
            '{{constructor_params}}' => $this->renderLines($params, indent: '            '),
            '{{array_fields}}' => $this->renderLines($arrayFields, indent: '            '),
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
                indent: '            ',
            ),
            ...$this->getSwaggerFields(setCommonProperties: false, setValidationAttributes: true),
        ];
    }

    private function getResourceReplacements(): array
    {
        return [
            '{{array_fields}}' => $this->renderLines(
                array_map(fn ($field) => "'{$field['name']}' => \$this->{$field['name']}", $this->entityFields()),
                indent: '            ',
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
                indent: '            ',
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
                indent: '            ',
            ),
            '{{display_field}}' => $displayField,
            '{{required_field}}' => $requiredField,
        ];
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
            '{{swagger_properties}}' => $this->renderLines($properties, indent: ' *      '),
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
}
