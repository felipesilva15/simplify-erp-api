<?php

namespace Tests\Feature\Console;

use App\Core\Exports\BaseExport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MakeModuleCrudExportTest extends TestCase
{
    private string $routesPath;

    private string $providersPath;

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
        $this->controllerPath = app_path('Core/Http/Controllers/Controller.php');

        // Garante estado limpo entre execuções da suíte
        $this->cleanupGeneratedFiles();

        // Tabelas necessárias para o comando inspecionar as colunas das entidades
        Schema::create('vehicles', function ($table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create('drivers', function ($table) {
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

    private function runModuleCrud(string $entity, array $options): int
    {
        $this->snapshotFiles();

        return Artisan::call('make:module-crud', [
            'module' => 'Sales',
            'entity' => $entity,
            ...$options,
        ]);
    }

    private function snapshotFiles(): void
    {
        $this->snapshots = [
            $this->routesPath => File::get($this->routesPath),
            $this->providersPath => File::get($this->providersPath),
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
            app_path('Providers/SalesModuleProvider.php'),
            app_path('Policies/VehiclePolicy.php'),
            app_path('Policies/DriverPolicy.php'),
            database_path('factories/VehicleFactory.php'),
            database_path('factories/DriverFactory.php'),
        ] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        foreach ([
            app_path('Modules/Sales'),
            base_path('tests/Feature/Sales'),
        ] as $directory) {
            if (File::isDirectory($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }

    public function test_command_generates_export_structure_when_export_flag_is_passed(): void
    {
        $exitCode = $this->runModuleCrud('Vehicle', ['--all' => true, '--export' => true]);

        $this->assertSame(0, $exitCode);

        // Export class gerada como subclasse de BaseExport
        $this->assertTrue(class_exists('App\Modules\Sales\Exports\VehicleExport'));
        $this->assertTrue(is_subclass_of('App\Modules\Sales\Exports\VehicleExport', BaseExport::class));

        // Controller utiliza o trait e implementa os métodos abstratos
        $controller = File::get(app_path('Modules/Sales/Http/Controllers/VehicleController.php'));
        $this->assertStringContainsString('use App\Core\Traits\HasExcelExport;', $controller);
        $this->assertStringContainsString('use App\Modules\Sales\Exports\VehicleExport;', $controller);
        $this->assertStringContainsString('use HasExcelExport;', $controller);
        $this->assertStringContainsString('protected function exportModelClass(): string', $controller);
        $this->assertStringContainsString('protected function exportClassForFormat(string $format): string', $controller);
        $this->assertStringContainsString("'full' => VehicleExport::class", $controller);

        // Policy ganha o método export
        $policy = File::get(app_path('Policies/VehiclePolicy.php'));
        $this->assertStringContainsString('public function export(User $user)', $policy);
        $this->assertStringContainsString("'vehicles.export'", $policy);

        // Rota única de export registrada antes do crudResource (sem rota custom separada)
        $routes = File::get($this->routesPath);
        $exportRoutePosition = strpos($routes, "Route::get('vehicles/export',");
        $crudRoutePosition = strpos($routes, "Route::crudResource('vehicles',");

        $this->assertNotFalse($exportRoutePosition);
        $this->assertNotFalse($crudRoutePosition);
        $this->assertLessThan($crudRoutePosition, $exportRoutePosition);
        $this->assertStringNotContainsString("Route::get('vehicles/export/{exportType}'", $routes);

        // Provider do módulo registrado
        $providers = File::get($this->providersPath);
        $this->assertStringContainsString('App\Providers\SalesModuleProvider::class', $providers);
    }

    public function test_command_does_not_generate_export_without_export_flag(): void
    {
        $exitCode = $this->runModuleCrud('Driver', ['--all' => true]);

        $this->assertSame(0, $exitCode);

        // Nenhum artefato de exportação deve existir
        $this->assertFalse(class_exists('App\Modules\Sales\Exports\DriverExport'));
        $this->assertFileDoesNotExist(app_path('Modules/Sales/Exports/DriverExport.php'));

        $controller = File::get(app_path('Modules/Sales/Http/Controllers/DriverController.php'));
        $this->assertStringNotContainsString('HasExcelExport', $controller);
        $this->assertStringNotContainsString('exportModelClass', $controller);

        $policy = File::get(app_path('Policies/DriverPolicy.php'));
        $this->assertStringNotContainsString('public function export(User', $policy);

        // Rotas de export não devem ser geradas
        $routes = File::get($this->routesPath);
        $this->assertStringNotContainsString("Route::get('drivers/export'", $routes);
        $this->assertStringContainsString("Route::crudResource('drivers',", $routes);
    }

    public function test_command_created_export_class_has_headings_and_mapping(): void
    {
        $this->runModuleCrud('Vehicle', ['--all' => true, '--export' => true]);

        $export = File::get(app_path('Modules/Sales/Exports/VehicleExport.php'));
        $this->assertStringContainsString('public function headings(): array', $export);
        $this->assertStringContainsString('public function map(mixed $row): array', $export);
        $this->assertStringContainsString("'ID'", $export);
        $this->assertStringContainsString("'Name'", $export);
    }
}
