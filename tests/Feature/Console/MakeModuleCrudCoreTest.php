<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MakeModuleCrudCoreTest extends TestCase
{
    private string $routesPath;

    private string $providersPath;

    private string $appCoreProviderPath;

    private string $controllerPath;

    /**
     * @var array<string, string> Conteúdo original dos arquivos alterados pelo comando
     */
    private array $snapshots = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->routesPath = base_path('routes/api.php');
        $this->providersPath = base_path('bootstrap/providers.php');
        $this->appCoreProviderPath = app_path('Providers/AppCoreProvider.php');
        $this->controllerPath = app_path('Core/Http/Controllers/Controller.php');

        // Garante estado limpo entre execuções da suíte
        $this->cleanupGeneratedFiles();

        // Tabela necessária para o comando inspecionar as colunas da entidade
        Schema::create('widgets', function ($table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        $this->restoreSnapshots();
        $this->cleanupGeneratedFiles();

        parent::tearDown();
    }

    private function runCoreCrud(string $entity, array $options): int
    {
        $this->snapshotFiles();

        return Artisan::call('make:module-crud', [
            'module' => 'Core',
            'entity' => $entity,
            ...$options,
        ]);
    }

    private function snapshotFiles(): void
    {
        $this->snapshots = [
            $this->routesPath => File::get($this->routesPath),
            $this->providersPath => File::get($this->providersPath),
            $this->appCoreProviderPath => File::get($this->appCoreProviderPath),
            $this->controllerPath => File::get($this->controllerPath),
        ];
    }

    private function restoreSnapshots(): void
    {
        foreach ($this->snapshots as $path => $content) {
            File::put($path, $content);
        }

        $this->snapshots = [];
    }

    private function cleanupGeneratedFiles(): void
    {
        foreach ([
            app_path('Core/Models/Widget.php'),
            app_path('Core/Services/WidgetService.php'),
            app_path('Core/Repositories/Eloquent/WidgetRepository.php'),
            app_path('Core/Repositories/Interfaces/WidgetRepositoryInterface.php'),
            app_path('Core/DTO/WidgetDTO.php'),
            app_path('Core/Http/Controllers/WidgetController.php'),
            app_path('Policies/WidgetPolicy.php'),
            database_path('factories/WidgetFactory.php'),
            base_path('tests/Feature/Core/WidgetTest.php'),
            app_path('Providers/CoreModuleProvider.php'),
        ] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        foreach ([
            app_path('Core/Http/Requests/Widget'),
            app_path('Core/Http/Resources/Widget'),
        ] as $directory) {
            if (File::isDirectory($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }

    public function test_command_generates_core_resources_under_core_folder_with_core_namespace(): void
    {
        $exitCode = $this->runCoreCrud('Widget', ['--all' => true]);

        $this->assertSame(0, $exitCode);

        $generatedFiles = [
            app_path('Core/Models/Widget.php'),
            app_path('Core/Services/WidgetService.php'),
            app_path('Core/Repositories/Eloquent/WidgetRepository.php'),
            app_path('Core/Repositories/Interfaces/WidgetRepositoryInterface.php'),
            app_path('Core/DTO/WidgetDTO.php'),
            app_path('Core/Http/Controllers/WidgetController.php'),
            app_path('Core/Http/Requests/Widget/StoreWidgetRequest.php'),
            app_path('Core/Http/Requests/Widget/UpdateWidgetRequest.php'),
            app_path('Core/Http/Resources/Widget/WidgetResource.php'),
            app_path('Core/Http/Resources/Widget/WidgetCollection.php'),
        ];

        foreach ($generatedFiles as $file) {
            $this->assertFileExists($file);
        }

        $this->assertStringContainsString('namespace App\Core\Models;', File::get(app_path('Core/Models/Widget.php')));
        $this->assertStringContainsString('namespace App\Core\DTO;', File::get(app_path('Core/DTO/WidgetDTO.php')));
        $this->assertStringContainsString('namespace App\Core\Repositories\Eloquent;', File::get(app_path('Core/Repositories/Eloquent/WidgetRepository.php')));
        $this->assertStringContainsString('namespace App\Core\Repositories\Interfaces;', File::get(app_path('Core/Repositories/Interfaces/WidgetRepositoryInterface.php')));
        $this->assertStringContainsString('namespace App\Core\Http\Controllers;', File::get(app_path('Core/Http/Controllers/WidgetController.php')));

        $controller = File::get(app_path('Core/Http/Controllers/WidgetController.php'));
        $this->assertStringContainsString('use App\Core\Http\Requests\Widget\StoreWidgetRequest;', $controller);
        $this->assertStringContainsString('use App\Core\Models\Widget;', $controller);
        $this->assertStringNotContainsString('App\Modules\Core', $controller);

        $model = File::get(app_path('Core/Models/Widget.php'));
        $this->assertStringNotContainsString('App\Modules\Core', $model);
    }

    public function test_command_registers_core_repository_binding_in_app_core_provider(): void
    {
        $exitCode = $this->runCoreCrud('Widget', ['--all' => true]);

        $this->assertSame(0, $exitCode);

        $provider = File::get($this->appCoreProviderPath);
        $this->assertStringContainsString('use App\Core\Repositories\Eloquent\WidgetRepository;', $provider);
        $this->assertStringContainsString('use App\Core\Repositories\Interfaces\WidgetRepositoryInterface;', $provider);
        $this->assertStringContainsString('$this->app->bind(WidgetRepositoryInterface::class, WidgetRepository::class);', $provider);

        // Nenhum provider dedicado do Core deve ser criado ou registrado
        $this->assertFileDoesNotExist(app_path('Providers/CoreModuleProvider.php'));
        $this->assertStringNotContainsString('CoreModuleProvider', File::get($this->providersPath));
    }

    public function test_command_registers_core_routes_and_swagger_tag(): void
    {
        $this->runCoreCrud('Widget', ['--all' => true]);

        $routes = File::get($this->routesPath);
        $this->assertStringContainsString('use App\Core\Http\Controllers\WidgetController;', $routes);
        $this->assertStringContainsString("Route::crudResource('widgets', WidgetController::class);", $routes);

        $swagger = File::get($this->controllerPath);
        $this->assertStringContainsString('"name"="Core"', $swagger);
        $this->assertStringContainsString('"tags"={"Module", "Resource", "Widget"}', $swagger);
    }
}