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
    protected $signature = 'make:module-crud {module} {entity}';
    protected $description = 'Create a new Domain Module Files (Repository, Service, Actions, DTO, etc)';
    
    private $rootPath = '';
    private $module = '';
    private $entity = '';
    private $modelFields = [];

    public function handle()
    {
        $this->module = Str::studly($this->argument('module'));
        $this->entity = Str::studly($this->argument('entity'));
        $this->rootPath = app_path("Modules/{$this->module}");

        $this->makeFolders();

        $this->loadModelAttributes();

        $this->createModel();
        $this->createService();
        $this->createRepository();
        $this->createRepositoryInterface();
        $this->createDto();
        $this->createController();
        $this->createRequests();
        $this->createResource();
        $this->createActions();

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
            ],
            [
                $dynamicReplacements['fillable_fields'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $this->entity . '.php';

        File::put($path, $content);
    }

    private function loadModelAttributes(): void {
        $entity = trim(strtolower($this->entity));
        $tableName = '';

        if (substr($this->entity, -1) == 's') {
            $tableName = $entity . 'es';
        } elseif (substr($this->entity, -1) == 'y') {
            $tableName = substr($entity, strlen($entity - 1)) . 'ies';
        } else {
            $tableName = $entity . 's';
        }

        $this->modelFields = ModelHelpers::getTableColumnsFromTable($tableName);
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
            ],
            [
                $dynamicReplacements['rules_definitions'],
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
            ],
            [
                $dynamicReplacements['rules_definitions'],
            ],
            $content
        );

        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Update' . $this->entity . 'Request.php';

        File::put($path, $content);
    }

    private function createResource(): void {
        $stub = $this->getStubContent('module.resource.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . $this->entity . 'Resource.php';

        File::put($path, $content);
    }

    private function createActions(): void {
        $this->createStoreAction();
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

        foreach ($this->modelFields as $index => $field) {
            $constructorProperties = $index == 0 ? $constructorProperties : $constructorProperties . str_pad('', 4 * 2, ' ');
            $constructorParams = $index == 0 ? $constructorParams : $constructorParams . str_pad('', 4 * 3, ' ');
            $arrayFields = $index == 0 ? $arrayFields : $arrayFields . str_pad('', 4 * 3, ' ');
            $literalDefaultValue = StringHelpers::toStringLiteral($field['default']);

            $constructorProperties = $constructorProperties . 'public ' . ($field['nullable'] ? '?' : '') .  $field['type'] . ' $' . $field['name'] . ' = ' . $literalDefaultValue . ',' . ($index + 1 < count($this->modelFields) ? PHP_EOL : '');
            $constructorParams = $constructorParams . $field['name'] . ": \$data['" . $field['name'] . "'] ?? " . $literalDefaultValue . ',' . ($index + 1 < count($this->modelFields) ? PHP_EOL : '');
            $arrayFields = $arrayFields . "'" . $field['name'] . "' => \$this->" . $field['name'] . ',' . ($index + 1 < count($this->modelFields) ? PHP_EOL : '');
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

        foreach ($this->modelFields as $index => $field) {
            if (in_array($field['name'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $definition = "'{$field['name']}' => '";
            $definition .= $field['nullable'] ? 'nullable' : 'required|';
            $definition .= $field['type'] == 'float' ? $typeRules[$field['type']] . ':' . (string) $field['precision'] : $typeRules[$field['type']] . '|';
            $definition .= $field['max_length'] && $field['type'] == 'string' ? 'min:1|max:' . (string) $field['max_length'] . '|' : '';
            $definition .= str_contains($field['name'], 'mail') ? 'email|' : ''; 
            $definition .= "',";
            $definition .= ($index + 1 < count($this->modelFields) ? PHP_EOL : '');

            $rulesDefinitions .= $rulesDefinitions == '' ? '' : str_pad('', 4 * 3, ' ');
            $rulesDefinitions .= $definition;
        }

        return [
            'rules_definitions' => $rulesDefinitions,
        ];
    }

    private function getModelDynamicReplacements(): array {
        $fillableFields = '';

        foreach ($this->modelFields as $index => $field) {
            if (in_array($field['name'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $fillableFields .= $fillableFields == '' ? '' : str_pad('', 4 * 2, ' ');
            $fillableFields .= "'{$field['name']}'," . ($index + 1 < count($this->modelFields) ? PHP_EOL : '');
        }

        return [
            'fillable_fields' => $fillableFields,
        ];
    }
}