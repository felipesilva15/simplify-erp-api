<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MakeModuleCrudAclTest extends TestCase
{
    private string $aclSeederPath;

    private string $routesPath;

    private string $providersPath;

    private string $controllerPath;

    private string $originalAclSeeder;

    /**
     * @var array<string, string> Conteúdo original dos arquivos alterados pelo comando
     */
    private array $snapshots = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->aclSeederPath = database_path('seeders/ACLSeeder.php');
        $this->routesPath = base_path('routes/api.php');
        $this->providersPath = base_path('bootstrap/providers.php');
        $this->controllerPath = app_path('Core/Http/Controllers/Controller.php');
        $this->originalAclSeeder = File::get($this->aclSeederPath);

        $this->cleanupGeneratedFiles();

        // Tabelas necessárias para o comando inspecionar as colunas das entidades
        Schema::create('warehouses', function ($table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create('suppliers', function ($table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        $this->restoreSnapshots();
        File::put($this->aclSeederPath, $this->originalAclSeeder);
        $this->cleanupGeneratedFiles();

        parent::tearDown();
    }

    private function runModuleCrud(string $module, string $entity, array $options): int
    {
        $this->snapshotFiles();

        return Artisan::call('make:module-crud', [
            'module' => $module,
            'entity' => $entity,
            ...$options,
        ]);
    }

    private function snapshotFiles(): void
    {
        if ($this->snapshots !== []) {
            return;
        }

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
            app_path('Providers/InventoryModuleProvider.php'),
            app_path('Policies/WarehousePolicy.php'),
            app_path('Policies/SupplierPolicy.php'),
            database_path('factories/WarehouseFactory.php'),
            database_path('factories/SupplierFactory.php'),
        ] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        foreach ([
            app_path('Modules/Inventory'),
            base_path('tests/Feature/Inventory'),
        ] as $directory) {
            if (File::isDirectory($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }

    public function test_command_adds_new_module_resource_and_permissions_to_acl_seeder(): void
    {
        $exitCode = $this->runModuleCrud('Inventory', 'Warehouse', ['--all' => true]);

        $this->assertSame(0, $exitCode);

        $content = File::get($this->aclSeederPath);

        $this->assertStringContainsString("'slug' => 'inventory',", $content);
        $this->assertStringContainsString("'name' => 'Inventory',", $content);
        $this->assertStringContainsString("'slug' => 'warehouses',", $content);
        $this->assertStringContainsString("'name' => 'Warehouses',", $content);
        $this->assertStringContainsString("'description' => 'Permite realizar a consulta de todos os warehouses',", $content);

        foreach (['viewAny', 'view', 'create', 'update', 'delete'] as $action) {
            $this->assertStringContainsString("'action' => '{$action}',", $content);
        }

        // A permissão de exportação só é criada com a flag correspondente
        $this->assertStringNotContainsString('Permite realizar a exportação de dados dos warehouses', $content);
    }

    public function test_command_adds_export_permission_when_export_flag_is_passed(): void
    {
        $this->runModuleCrud('Inventory', 'Warehouse', ['--policy' => true, '--export' => true]);

        $content = File::get($this->aclSeederPath);

        $this->assertStringContainsString("'action' => 'export',", $content);
        $this->assertStringContainsString('Permite realizar a exportação de dados dos warehouses', $content);
    }

    public function test_command_reuses_existing_module_instead_of_duplicating_it(): void
    {
        $exitCode = $this->runModuleCrud('ThirdParty', 'Supplier', ['--policy' => true]);

        $this->assertSame(0, $exitCode);

        $content = File::get($this->aclSeederPath);

        $this->assertSame(1, substr_count($content, "'slug' => 'thirdParty',"));
        $this->assertStringNotContainsString("'description' => 'Módulo ThirdParty do sistema',", $content);
        $this->assertStringContainsString("'slug' => 'suppliers',", $content);
        $this->assertStringContainsString("'description' => 'Permite realizar a consulta de todos os suppliers',", $content);
    }

    public function test_command_is_idempotent_and_complements_missing_permissions(): void
    {
        $this->runModuleCrud('Inventory', 'Warehouse', ['--policy' => true]);
        $this->runModuleCrud('Inventory', 'Warehouse', ['--policy' => true, '--export' => true]);

        $content = File::get($this->aclSeederPath);

        $this->assertSame(1, substr_count($content, "'slug' => 'inventory',"));
        $this->assertSame(1, substr_count($content, "'slug' => 'warehouses',"));
        $this->assertSame(1, substr_count($content, 'Permite realizar a consulta de todos os warehouses'));
        $this->assertSame(1, substr_count($content, 'Permite realizar a exportação de dados dos warehouses'));
    }
}
