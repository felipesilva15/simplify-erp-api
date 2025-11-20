<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module {module} {entity}';
    protected $description = 'Create a new Domain Module Files (Repository, Service, Actions, DTO, etc)';
    
    private $rootPath = '';
    private $module = '';
    private $entity = '';

    public function handle()
    {
        $this->module = Str::studly($this->argument('module'));
        $this->entity = Str::studly($this->argument('entity'));
        $this->rootPath = app_path("Modules/{$this->module}");

        $this->makeFolders();

        $this->createModel();
        $this->createService();
        $this->createRepository();
        $this->createRepositoryInterface();
        $this->createDto();
        $this->createController();
        $this->createRequests();

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
            "{$this->rootPath}/Http/Resources/{$this->entity}",
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
                '{{module}}'
            ],
            [
                $this->entity,
                $this->module
            ],
            $stub
        );
    }

    private function createModel(): void {
        $stub = $this->getStubContent('module.model.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $this->entity . '.php';

        File::put($path, $content);
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
        $content = $this->replaceDefaultStubPlaceholders($stub);
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
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Store' . $this->entity . 'Request.php';

        File::put($path, $content);
    }

    private function createUpdateRequest(): void {
        $stub = $this->getStubContent('module.request-store.stub');
        $content = $this->replaceDefaultStubPlaceholders($stub);
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $this->entity . DIRECTORY_SEPARATOR . 'Store' . $this->entity . 'Request.php';

        File::put($path, $content);
    }
}