<?php

namespace Tests\Feature\Core;

use App\Core\Exports\BaseExport;
use App\Core\Http\Controllers\Controller;
use App\Core\Repositories\Eloquent\BaseRepository;
use App\Core\Traits\HasExcelExport;
use App\Modules\Security\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * Model de teste reutilizando a tabela `countries` (migração já existente),
 * evitando a necessidade de um módulo dedicado apenas para os testes.
 */
class Gadget extends Model
{
    protected $table = 'countries';

    protected $guarded = [];
}

class GadgetRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return Gadget::class;
    }
}

class GadgetService
{
    public function __construct(protected GadgetRepository $repository) {}

    public function exportQuery(array $params = []): Builder
    {
        return $this->repository->getExportQuery($params);
    }
}

class GadgetExport extends BaseExport
{
    public function headings(): array
    {
        return ['ID', 'ISO Code', 'Name'];
    }

    public function map(mixed $row): array
    {
        return [$row->id, $row->iso_code, $row->name];
    }
}

class GadgetSummaryExport extends BaseExport
{
    public function headings(): array
    {
        return ['ID', 'Name'];
    }

    public function map(mixed $row): array
    {
        return [$row->id, $row->name];
    }
}

/**
 * Policy de teste para validar que a rota de exportação exige
 * autorização através do método `export` da policy.
 */
class GadgetPolicy
{
    public function export(User $user): bool
    {
        return $user->is_admin;
    }
}

class GadgetController extends Controller
{
    use HasExcelExport;

    protected GadgetService $service;

    public function __construct(GadgetService $service)
    {
        $this->service = $service;
    }

    protected function exportModelClass(): string
    {
        return Gadget::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => GadgetExport::class,
            'summarized' => GadgetSummaryExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }
}

class ExcelExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Gate::policy(Gadget::class, GadgetPolicy::class);

        Route::prefix('api')->middleware('auth')->group(function () {
            Route::prefix('gadgets')->group(function () {
                Route::get('export', [GadgetController::class, 'export'])->name('gadgets.export');
            });
        });
    }

    private function makeGadgets(): void
    {
        Gadget::create(['iso_code' => 'BR', 'name' => 'Brasil']);
        Gadget::create(['iso_code' => 'US', 'name' => 'Estados Unidos']);
        Gadget::create(['iso_code' => 'DE', 'name' => 'Alemanha']);
    }

    public function test_can_export_all_records_as_default_excel_file(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson('/api/gadgets/export', $this->getAdminAuthHeaders());

        $response->assertOk();

        Excel::assertDownloaded('gadget.xlsx', function (GadgetExport $export) {
            return $export->query()->count() === 3;
        });
    }

    public function test_export_uses_same_filters_as_index(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson(
            '/api/gadgets/export?filters[id][eq]=2',
            $this->getAdminAuthHeaders()
        );

        $response->assertOk();

        Excel::assertDownloaded('gadget.xlsx', function (GadgetExport $export) {
            return $export->query()->pluck('id')->all() === [2];
        });
    }

    public function test_export_uses_same_like_filter_as_index(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson(
            '/api/gadgets/export?filters[name][like]=il&sorts=id',
            $this->getAdminAuthHeaders()
        );

        $response->assertOk();

        Excel::assertDownloaded('gadget.xlsx', function (GadgetExport $export) {
            return $export->query()->pluck('name')->all() === ['Brasil'];
        });
    }

    public function test_can_export_with_custom_format(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson('/api/gadgets/export?format=summarized', $this->getAdminAuthHeaders());

        $response->assertOk();

        Excel::assertDownloaded('gadget-summarized.xlsx', function ($export) {
            return $export instanceof GadgetSummaryExport
                && $export->headings() === ['ID', 'Name'];
        });
    }

    public function test_can_choose_extension_on_default_export(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson('/api/gadgets/export?extension=xls', $this->getAdminAuthHeaders());

        $response->assertOk();

        Excel::assertDownloaded('gadget.xls', function (GadgetExport $export) {
            return $export->query()->count() === 3;
        });
    }

    public function test_can_choose_extension_on_custom_format(): void
    {
        $this->makeGadgets();

        Excel::fake();

        $response = $this->getJson('/api/gadgets/export?format=summarized&extension=csv', $this->getAdminAuthHeaders());

        $response->assertOk();

        Excel::assertDownloaded('gadget-summarized.csv', function ($export) {
            return $export instanceof GadgetSummaryExport
                && $export->headings() === ['ID', 'Name'];
        });
    }

    public function test_cannot_export_with_unsupported_extension(): void
    {
        Excel::fake();

        $response = $this->getJson('/api/gadgets/export?extension=pdf', $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cannot_export_with_unknown_format(): void
    {
        Excel::fake();

        $response = $this->getJson('/api/gadgets/export?format=unknown', $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function test_cannot_export_without_authentication(): void
    {
        Excel::fake();

        $response = $this->getJson('/api/gadgets/export');

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_export_without_permission(): void
    {
        Excel::fake();

        $response = $this->getJson('/api/gadgets/export', $this->getCommomUserAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
