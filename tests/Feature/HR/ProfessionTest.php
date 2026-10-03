<?php

namespace Tests\Feature\HR;

use App\Modules\HR\Exports\ProfessionExport;
use App\Modules\HR\Models\Profession;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProfessionTest extends TestCase
{
    protected string $endpoint = '/api/hr/professions';

    protected function getResourceStructure(): array {
        return [
            'id',
            'cbo',
            'name',
            'created_at',
            'updated_at'
        ];
    }

    protected function getLookupResourceStructure(): array {
        return [
            'key',
            'label',
            'sublabel',
            'meta' => [
                'id',
                'name',
                'cbo'
            ]
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_professions(): void
    {
        Profession::factory(3)->create();
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_professions_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        Profession::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_professions_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        Profession::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_cannot_list_professions_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_professions_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_profession_by_id(): void
    {
        $model = Profession::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.cbo', $model->cbo)
            ->assertJsonPath('data.name', $model->name);
    }

    public function test_cannot_get_profession_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_profession_by_id_without_authentication(): void
    {
        $model = Profession::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_profession_by_id_without_permission(): void
    {
        $model = Profession::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_cannot_get_profession_for_edit(): void
    {
        $model = Profession::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_lookup_returns_default_api_response_structure(): void {
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_lookup_professions(): void
    {
        Profession::factory(3)->create();
        $response = $this->getJson("{$this->endpoint}/lookup", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getLookupResourceStructure()
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_lookup_professions_with_text_filter(): void
    {
        $profession = Profession::factory()->createOne(['name' => 'engenheiro civil']);
        Profession::factory()->createOne(['name' => 'médico']);

        $queryParams = [
            'q' => 'engenheiro'
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.key', $profession->id)
            ->assertJsonPath('data.0.label', 'engenheiro civil')
            ->assertJsonPath('data.0.sublabel', "Cod.: {$profession->id}")
            ->assertJsonPath('data.0.meta.cbo', $profession->cbo);
    }

    public function test_can_lookup_professions_filtered_by_keys(): void
    {
        $professions = Profession::factory(3)->create();
        $queryParams = [
            'keys' => [
                $professions[0]->id,
                $professions[2]->id
            ]
        ];

        $response = $this->getJson(url()->query("{$this->endpoint}/lookup", $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['key' => $professions[0]->id])
            ->assertJsonFragment(['key' => $professions[2]->id]);
    }

    public function test_cannot_lookup_professions_without_authentication(): void
    {
        $response = $this->getJson("{$this->endpoint}/lookup");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_export_professions_as_default_excel_file(): void
    {
        Profession::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('profession.xlsx', function (ProfessionExport $export) {
            return $export->query()->count() === 3
                && $export->headings() === ['ID', 'CBO', 'Nome'];
        });
    }

    public function test_can_export_professions_with_filter(): void
    {
        Profession::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?filters[id][eq]=2",
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('profession.xlsx', function (ProfessionExport $export) {
            return $export->query()->pluck('id')->all() === [2];
        });
    }

    public function test_can_export_professions_with_explicit_format(): void
    {
        Profession::factory(2)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export?format=full", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('profession.xlsx', function (ProfessionExport $export) {
            return $export->query()->count() === 2;
        });
    }

    public function test_cannot_export_professions_with_unsupported_extension(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export?extension=pdf", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cannot_export_professions_without_authentication(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_export_professions_without_permission(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getCommomUserAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }
}
