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
                            {--action : Whether the actions should be created}';
    protected $description = 'Create a new domain module files (Repository, Service, Actions, DTO, etc)';

    private $rootPath = '';
    private $module = '';
    private $entity = '';
    private $entityFields = [];
    private $commomFields = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function handle()
    {
        $this->module = Str::studly($this->argument('module'));
        $this->entity = Str::studly($this->argument('entity'));
        $this->rootPath = app_path("Modules/{$this->module}");

        $this->makeFolders();
        $this->loadEntityFields();

        if ($this->option('all') || $this->option('model')) {
            $this->createModel();
        } 
        
        if ($this->option('all') || $this->option('service')) {
            $this->createService();
        }

        if ($this->option('all') || $this->option('repository')) {
            $this->createRepository();
            $this->createRepositoryInterface();
        }

        if ($this->option('all') || $this->option('dto')) {
            $this->createDto();
        }

        if ($this->option('all') || $this->option('controller')) {
            $this->createController();
        }

        if ($this->option('all') || $this->option('request')) {
            $this->createRequests();
        }

        if ($this->option('all') || $this->option('resource')) {
            $this->createResource();
        }

        if ($this->option('all') || $this->option('action')) {
            $this->createActions();
        }

        $this->info("Module [{$this->rootPath}] created successfully for {$this->entity} Entity!");
    }

    private function makeFolders(): void {
        $folders = [
            $this->rootPath,
            "{$this->rootPath}/Models",
            "{$this->rootPath}/Services",
            "{$this->rootPath}/Repositories/Eloquent",
            "{$this->rootPath}/Repositories/Interfaces",
            "{$this->rootPath}/Actions/{$this->entity}",
            "{$this->rootPath}/DTO",
            "{$this->rootPath}/Http/Controllers",
            "{$this->rootPath}/Http/Requests/{$this->entity}",
            "{$this->rootPath}/Http/Resources",
        ];

        foreach ($folders as $folder) {
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }
        }
    }

    private function getStubsPath(): string {
        return app_path('Console/Stubs');
    }

    private function getStubContent(string $name): string {
        return File::get($this->getStubsPath() . DIRECTORY_SEPARATOR . $name);
    }

    private function replaceDefaultStubPlaceholders(string $stub): string {
        return str_replace(
            [
                '{{entity}}',
                '{{lower_entity}}',
                '{{module}}',
                '{{lower_module}}'
            ],
            [
                $this->entity,
                strtolower($this->entity),
                $this->module,
                strtolower($this->module)
            ],
            $stub
        );
    }

    private function createModel(): void {
        $stub = $this->getStubContent('module.model.stub');
        
        $dynamicReplacements = $this->getModelDynamicReplacements();
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $content = str_replace(
            [
                '{{fillable_fields}}',
                '{{swagger_properties}}',
            ],
            [
                $dynamicReplacements['fillable_fields'],
                $dynamicReplacements['swagger_properties'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $this->entity . '.php';

        File::put($path, $content);
    }

    private function loadEntityFields(): void {
        $entity = trim(strtolower($this->entity));
        $tableName = '';

        if (substr($this->entity, -1) == 's') {
            $tableName = $entity . 'es';
        } elseif (substr($this->entity, -1) == 'y') {
            $tableName = substr($entity, strlen($entity - 1)) . 'ies';
        } else {
            $tableName = $entity . 's';
        }

        $this->entityFields = ModelHelpers::getTableColumnsFromTable($tableName);
    }

    private function createRepository(): void {
        $stub = $this->getStubContent('module.repository.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'Eloquent' . DIRECTORY_SEPARATOR . $this->entity . 'Repository.php';

        File::put($path, $content);
    }

    private function createRepositoryInterface(): void {
        $stub = $this->getStubContent('module.repository-interface.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'Interfaces' . DIRECTORY_SEPARATOR . $this->entity . 'RepositoryInterface.php';

        File::put($path, $content);
    }

    private function createService(): void {
        $stub = $this->getStubContent('module.service.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $this->entity . 'Service.php';

        File::put($path, $content);
    }

    private function createDto(): void {
        $stub = $this->getStubContent('module.dto.stub');

        $dynamicReplacements = $this->getDtoDynamicReplacements();
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $content = str_replace(
            [
                '{{constructor_properties}}',
                '{{constructor_params}}',
                '{{array_fields}}',
            ],
            [
                $dynamicReplacements['constructor_properties'],
                $dynamicReplacements['constructor_params'],
                $dynamicReplacements['array_fields'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'DTO' . DIRECTORY_SEPARATOR . $this->entity . 'DTO.php';

        File::put($path, $content);
    }

    private function createController(): void {
        $stub = $this->getStubContent('module.controller.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $this->entity . 'Controller.php';

        File::put($path, $content);
    }

    private function createRequests(): void {
        $this->createStoreRequest();
        $this->createUpdateRequest();
    }

    private function createStoreRequest(): void {
        $stub = $this->getStubContent('module.request-store.stub');
        
        $dynamicReplacements = $this->getRequestDynamicReplacements();
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $content = str_replace(
            [
                '{{rules_definitions}}',
                '{{swagger_required}}',
                '{{swagger_properties}}',
            ],
            [
                $dynamicReplacements['rules_definitions'],
                $dynamicReplacements['swagger_required'],
                $dynamicReplacements['swagger_properties'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Store' . $this->entity . 'Request.php';

        File::put($path, $content);
    }

    private function createUpdateRequest(): void {
        $stub = $this->getStubContent('module.request-update.stub');
        
        $dynamicReplacements = $this->getRequestDynamicReplacements();
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $content = str_replace(
            [
                '{{rules_definitions}}',
                '{{swagger_required}}',
                '{{swagger_properties}}',
            ],
            [
                $dynamicReplacements['rules_definitions'],
                $dynamicReplacements['swagger_required'],
                $dynamicReplacements['swagger_properties'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Update' . $this->entity . 'Request.php';

        File::put($path, $content);
    }

    private function createResource(): void {
        $stub = $this->getStubContent('module.resource.stub');
        
        $dynamicReplacements = $this->getResourceDynamicReplacements();
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $content = str_replace(
            [
                '{{array_fields}}',
                '{{swagger_properties}}',
            ],
            [
                $dynamicReplacements['array_fields'],
                $dynamicReplacements['swagger_properties'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . $this->entity . 'Resource.php';

        File::put($path, $content);
    }

    private function createActions(): void {
        $this->createStoreAction();
        $this->createEditAction();
        $this->createUpdateAction();
        $this->createDeleteAction();
        $this->createShowAction();
        $this->createListAction();
    }

    private function createStoreAction(): void {
        $stub = $this->getStubContent('module.action-store.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Store' . $this->entity . 'Action.php';

        File::put($path, $content);
    }

    private function createEditAction(): void {
        $stub = $this->getStubContent('module.action-edit.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Edit' . $this->entity . 'Action.php';

        File::put($path, $content);
    }

    private function createUpdateAction(): void {
        $stub = $this->getStubContent('module.action-update.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Update' . $this->entity . 'Action.php';

        File::put($path, $content);
    }

    private function createDeleteAction(): void {
        $stub = $this->getStubContent('module.action-delete.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Delete' . $this->entity . 'Action.php';

        File::put($path, $content);
    }

    private function createShowAction(): void {
        $stub = $this->getStubContent('module.action-show.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Show' . $this->entity . 'Action.php';

        File::put($path, $content);
    }

    private function createListAction(): void {
        $stub = $this->getStubContent('module.action-list.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Actions' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'List' . $this->entity . 'Action.php';

        File::put($path, $content);

        Carbon::now();
    }

    private function getDtoDynamicReplacements(): array {
        $constructorProperties = '';
        $constructorParams = '';
        $arrayFields = '';

        foreach ($this->entityFields as $index => $field) {
            $constructorProperties = $index == 0 ? $constructorProperties : $constructorProperties . str_pad('', 4 * 2, ' ');
            $constructorParams = $index == 0 ? $constructorParams : $constructorParams . str_pad('', 4 * 3, ' ');
            $arrayFields = $index == 0 ? $arrayFields : $arrayFields . str_pad('', 4 * 3, ' ');
            $literalDefaultValue = StringHelpers::toStringLiteral($field['default']);

            $constructorProperties = $constructorProperties . 'public ' . ($field['nullable'] ? '?' : '') .  $field['type'] . ' $' . $field['name'] . ' = ' . $literalDefaultValue . ',' . ($index + 1 < count($this->entityFields) ? PHP_EOL : '');
            $constructorParams = $constructorParams . $field['name'] . ": \$data['" . $field['name'] . "'] ?? " . $literalDefaultValue . ',' . ($index + 1 < count($this->entityFields) ? PHP_EOL : '');
            $arrayFields = $arrayFields . "'" . $field['name'] . "' => \$this->" . $field['name'] . ',' . ($index + 1 < count($this->entityFields) ? PHP_EOL : '');
        }

        return [
            'constructor_properties' => $constructorProperties,
            'constructor_params' => $constructorParams,
            'array_fields' => $arrayFields
        ];
    }

    private function getRequestDynamicReplacements(): array {
        $rulesDefinitions = '';

        $typeRules = [
            'string' => 'string',
            'float' => 'decimal',
            'int' => 'integer',
            'Carbon' => 'datetime',
            'bool' => 'boolean',
        ];

        $swaggerReplacements = $this->getSwaggerFieldsAnnotation(setCommomProperties: false, setValidationAttributes: true);

        foreach ($this->entityFields as $index => $field) {
            if (in_array($field['name'], $this->commomFields)) {
                continue;
            }

            $definition = "'{$field['name']}' => '";
            $definition .= $field['nullable'] ? 'nullable' : 'required|';
            $definition .= $field['type'] == 'float' ? $typeRules[$field['type']] . ':' . (string) $field['precision'] : $typeRules[$field['type']] . '|';
            $definition .= $field['max_length'] && $field['type'] == 'string' ? 'min:1|max:' . (string) $field['max_length'] . '|' : '';
            $definition .= str_contains($field['name'], 'mail') ? 'email|' : ''; 
            $definition .= "',";
            $definition .= ($index + 1 < count($this->entityFields) ? PHP_EOL : '');

            $rulesDefinitions .= $rulesDefinitions == '' ? '' : str_pad('', 4 * 3, ' ');
            $rulesDefinitions .= $definition;
        }

        return [
            'rules_definitions' => $rulesDefinitions,
            'swagger_required' => $swaggerReplacements['required'],
            'swagger_properties' => $swaggerReplacements['properties'],
        ];
    }

    private function getModelDynamicReplacements(): array {
        $fillableFields = '';

        $swaggerReplacements = $this->getSwaggerFieldsAnnotation(setCommomProperties: true, setValidationAttributes: false);

        foreach ($this->entityFields as $index => $field) {
            if (in_array($field['name'], $this->commomFields)) {
                continue;
            }

            $fillableFields .= $fillableFields == '' ? '' : str_pad('', 4 * 2, ' ');
            $fillableFields .= "'{$field['name']}'," . ($index + 1 < count($this->entityFields) ? PHP_EOL : '');
        }

        return [
            'fillable_fields' => $fillableFields,
            'swagger_properties' => $swaggerReplacements['properties'],
        ];
    }

    private function getResourceDynamicReplacements(): array {
        $arrayFields = '';

        $swaggerReplacements = $this->getSwaggerFieldsAnnotation(setCommomProperties: true, setValidationAttributes: true);

        foreach ($this->entityFields as $index => $field) {
            $arrayFields = $index == 0 ? $arrayFields : $arrayFields . str_pad('', 4 * 3, ' ');

            $arrayFields = $arrayFields . "'" . $field['name'] . "' => \$this->" . $field['name'] . ',' . ($index + 1 < count($this->entityFields) ? PHP_EOL : '');
        }

        return [
            'array_fields' => $arrayFields,
            'swagger_properties' => $swaggerReplacements['properties'],
        ];
    }

    private function getSwaggerFieldsAnnotation(bool $setCommomProperties, bool $setValidationAttributes): array {
        $required = '';
        $properties = '';

        $typeMap = [
            'string' => [
                'type' => 'string',
                'format' => '', 
                'example' => '"Sample"'
            ],
            'float' => [
                'type' => 'number', 
                'format' => 'float', 
                'example' => '20.99'
            ],
            'int' => [
                'type' => 'integer', 
                'format' => '', 
                'example' => '1'
            ],
            'Carbon' => [
                'type' => 'string', 
                'format' => 'date-time', 
                'example' => '"' . Carbon::now()->toISOString(). '"'
            ],
            'bool' => [
                'type' => 'boolean', 
                'format' => '', 
                'example' => 'false'
            ],
        ];


        foreach ($this->entityFields as $field) {
            if (!$setCommomProperties && in_array($field['name'], $this->commomFields)) {
                continue;
            }

            $property = '';

            if ($properties != '')
                $property .= ' *  ' . str_pad('', 4 * 1, ' ');

            $type = $typeMap[$field['type']];

            $property .= "@OA\Property(property=\"{$field['name']}\", type=\"{$type['type']}\"";

            if ($type['format'] ?? '')
                $property .= ", format=\"{$type['format']}\"";

            $property .= ', example=' . $type['example'];

            if ($setValidationAttributes) {
                if (!$field['nullable'])
                    $required .= ',"'. $field['name'] .'"';

                switch ($field['type']) {
                    case 'string':
                        $property .= ', minLength=1, maxLength=' . $field['max_length'];
                        break;

                    case 'float':
                        $property .= ', minimum=0.' . str_pad('', $field['precision'] - 1, '0') . '1';
                        $property .= ', maximum=' . str_pad('', $field['max_length'] - $field['precision'], '9') . '.' . str_pad('', $field['precision'], '9');
                        break;
                }
            }

            if ($field['nullable'])
                $property .= ', nullable=true';

            $property .= '),' . PHP_EOL;
            $properties .= $property;
        }

        $required = substr($required, 1);
        $properties = substr($properties, 0, strlen($properties) - (1 + strlen(PHP_EOL)));

        return [
            'required' => $required,
            'properties' => $properties
        ];
    }
}