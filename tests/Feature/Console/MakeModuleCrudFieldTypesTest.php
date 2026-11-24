<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MakeModuleCrudFieldTypesTest extends TestCase
{
    private string $migrationPath;

    private array $snapshots = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrationPath = database_path('migrations/9999_99_99_999999_create_payment_intents_table.php');

        $this->cleanupGeneratedFiles();

        File::put($this->migrationPath, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('payment_type', 2);
            $table->string('applicable_type', 30);
            $table->string('tax_type', 30)->nullable();
            $table->decimal('tax_aliquot', 7, 4)->nullable();
            $table->decimal('processing_fee', 15, 2);
            $table->string('notes', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
PHP);

        Schema::create('payment_intents', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->string('payment_type', 2);
            $table->string('applicable_type', 30);
            $table->string('tax_type', 30)->nullable();
            $table->decimal('tax_aliquot', 7, 4)->nullable();
            $table->decimal('processing_fee', 15, 2);
            $table->string('notes', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        $this->restoreSnapshots();
        $this->cleanupGeneratedFiles();

        parent::tearDown();
    }

    public function test_command_generates_nullable_decimal_and_enum_code_across_artifacts(): void
    {
        $this->snapshotFiles();

        $exitCode = Artisan::call('make:module-crud', [
            'module' => 'Finance',
            'entity' => 'PaymentIntent',
            '--all' => true,
            '--lookup' => true,
            '--export' => true,
        ]);

        $this->assertSame(0, $exitCode, Artisan::output());

        $dto = File::get(app_path('Modules/Finance/DTO/PaymentIntentDTO.php'));
        $this->assertStringContainsString('public ?string $notes = null', $dto);
        $this->assertStringContainsString("notes: \$data['notes'] ?? null", $dto);
        $this->assertStringContainsString('public ?string $tax_aliquot = null', $dto);
        $this->assertStringContainsString("tax_aliquot: \$data['tax_aliquot'] ?? null", $dto);
        $this->assertStringContainsString("public string \$processing_fee = '0'", $dto);

        $storeRequest = File::get(app_path('Modules/Finance/Http/Requests/PaymentIntent/StorePaymentIntentRequest.php'));
        $this->assertStringContainsString('use App\Modules\Finance\Enums\PaymentTypeEnum;', $storeRequest);
        $this->assertStringContainsString('use Illuminate\Validation\Rule;', $storeRequest);
        $this->assertStringContainsString("'payment_type' => ['required', Rule::enum(PaymentTypeEnum::class)]", $storeRequest);
        $this->assertStringContainsString("'applicable_type' => ['required', Rule::enum(FinanceApplicabilityEnum::class)]", $storeRequest);
        $this->assertStringContainsString("'tax_type' => ['nullable', Rule::enum(TaxTypeEnum::class)]", $storeRequest);
        $this->assertStringContainsString("'tax_aliquot' => 'nullable|decimal:0,4'", $storeRequest);
        $this->assertStringContainsString("'processing_fee' => 'required|decimal:0,2'", $storeRequest);
        $this->assertStringContainsString('@OA\Property(property="payment_type", ref="#/components/schemas/PaymentTypeEnum")', $storeRequest);
        $this->assertStringContainsString('@OA\Property(property="tax_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true)', $storeRequest);

        $model = File::get(app_path('Modules/Finance/Models/PaymentIntent.php'));
        $this->assertStringContainsString('use App\Modules\Finance\Enums\PaymentTypeEnum;', $model);
        $this->assertStringContainsString("'payment_type' => PaymentTypeEnum::class", $model);
        $this->assertStringContainsString("'tax_aliquot' => 'decimal:4'", $model);
        $this->assertStringContainsString("'processing_fee' => 'decimal:2'", $model);
        $this->assertStringContainsString("'is_active' => 'boolean'", $model);

        $resource = File::get(app_path('Modules/Finance/Http/Resources/PaymentIntent/PaymentIntentResource.php'));
        $this->assertStringContainsString('@OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum")', $resource);
        $this->assertStringContainsString('@OA\Property(property="processing_fee", type="string", format="decimal"', $resource);

        $factory = File::get(database_path('factories/PaymentIntentFactory.php'));
        $this->assertStringContainsString('use App\Modules\Finance\Enums\PaymentTypeEnum;', $factory);
        $this->assertStringContainsString('fake()->randomElement(PaymentTypeEnum::cases())', $factory);
        $this->assertStringContainsString("fake()->boolean() ? null : number_format(fake()->randomFloat(4, 0, 999.9999), 4, '.', '')", $factory);

        $controller = File::get(app_path('Modules/Finance/Http/Controllers/PaymentIntentController.php'));
        $this->assertSame(0, preg_match('/^ \*     @OA\\\\Parameter\(name="filters/m', $controller));
        $this->assertMatchesRegularExpression('/^ \*         @OA\\\\Parameter\(name="filters\[payment_type\]\[eq\]"/m', $controller);
        $this->assertDoesNotMatchRegularExpression('/\{\{[a-z_]+\}\}/', $controller);
    }

    private function snapshotFiles(): void
    {
        if ($this->snapshots !== []) {
            return;
        }

        foreach ([
            base_path('routes/api.php'),
            base_path('bootstrap/providers.php'),
            base_path('app/Core/Http/Controllers/Controller.php'),
            base_path('database/seeders/ACLSeeder.php'),
            app_path('Providers/FinanceModuleProvider.php'),
        ] as $path) {
            $this->snapshots[$path] = File::get($path);
        }
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
            app_path('Modules/Finance/Models/PaymentIntent.php'),
            app_path('Modules/Finance/DTO/PaymentIntentDTO.php'),
            app_path('Modules/Finance/Http/Controllers/PaymentIntentController.php'),
            app_path('Modules/Finance/Repositories/Eloquent/PaymentIntentRepository.php'),
            app_path('Modules/Finance/Repositories/Interfaces/PaymentIntentRepositoryInterface.php'),
            app_path('Modules/Finance/Services/PaymentIntentService.php'),
            app_path('Modules/Finance/Exports/PaymentIntentExport.php'),
            app_path('Policies/PaymentIntentPolicy.php'),
            database_path('factories/PaymentIntentFactory.php'),
            base_path('tests/Feature/Finance/PaymentIntentTest.php'),
            $this->migrationPath,
        ] as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        foreach ([
            app_path('Modules/Finance/Http/Requests/PaymentIntent'),
            app_path('Modules/Finance/Http/Resources/PaymentIntent'),
        ] as $directory) {
            if (File::isDirectory($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }
}
