<?php

namespace Tests\Feature\Partner;

use App\Modules\Partner\Exports\PartnerExport;
use App\Modules\Partner\Models\Partner;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    protected string $endpoint = '/api/partner/partners';

    protected function getResourceStructure(): array {
        return [
            'id',
            'partner_type_code',
            'name',
            'trade_name',
            'person_type',
            'taxpayer_type',
            'document_number',
            'identity_number',
            'identity_issuer',
            'partner_since',
            'state_registration',
            'municipal_registration',
            'suframa_registration',
            'marital_status',
            'cbo',
            'gender',
            'birth_date',
            'father_name',
            'father_document',
            'mother_name',
            'mother_document',
            'pix_type',
            'pix_key',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
    }

    public function test_listing_returns_default_api_response_structure(): void {
        $response = $this->getJson($this->endpoint, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
    }

    public function test_can_list_partners(): void
    {
        Partner::factory(3)->create();
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

    public function test_can_list_partners_with_sort(): void
    {
        $queryParams = [
            'sorts' => '-id'
        ];

        Partner::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonPath('data.0.id', 3);
    }

    public function test_can_list_partners_with_filter(): void
    {
        $queryParams = [
            'filters' => [
                'id' => [
                    'eq' => 2
                ]
            ]
        ];

        Partner::factory(3)->create();
        $response = $this->getJson(url()->query($this->endpoint, $queryParams), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 2);
    }

    public function test_cannot_list_partners_without_authentication(): void
    {
        $response = $this->getJson($this->endpoint);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_list_partners_without_permission(): void
    {
        $response = $this->getJson($this->endpoint, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_partner_by_id(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.partner_type_code', $model->partner_type_code);
    }

    public function test_cannot_get_partner_by_invalid_id(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999", $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_partner_by_id_without_authentication(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_partner_by_id_without_permission(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_get_partner_by_id_for_edit(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.partner_type_code', $model->partner_type_code)
            ->assertJsonPath('meta.editable', true);
    }

    public function test_cannot_get_partner_by_invalid_id_for_edit(): void
    {
        $response = $this->getJson("{$this->endpoint}/999999/edit", $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_get_partner_by_id_for_edit_without_authentication(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_get_partner_by_id_for_edit_without_permission(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->getJson("{$this->endpoint}/{$model->id}/edit", $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_create_partner(): void
    {
        $model = Partner::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.partner_type_code', $model->partner_type_code);

        $this->assertDatabaseHas('partners', [
            'partner_type_code' => $model->partner_type_code,
        ]);
    }

    public function test_cannot_create_partner_with_invalid_payload(): void
    {
        $model = Partner::factory()->makeOne();
        $data = $model->toArray();
        unset($data['partner_type_code']);

        $response = $this->postJson($this->endpoint, $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('partner_type_code');
    }

    public function test_cannot_create_partner_without_authentication(): void
    {
        $model = Partner::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_create_partner_without_permission(): void
    {
        $model = Partner::factory()->makeOne();
        $data = $model->toArray();

        $response = $this->postJson($this->endpoint, $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_update_partner(): void
    {
        $model = Partner::factory()->createOne();

        $data = $model->toArray();
        $data['partner_type_code'] = 'Updated Partner';

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonIsObject()
            ->assertJsonStructure([
                'data' => $this->getResourceStructure()
            ])
            ->assertJsonPath('data.partner_type_code', 'Updated Partner');
    }

    public function test_cannot_update_partner_with_invalid_payload(): void
    {
        $model = Partner::factory()->createOne();

        $data = $model->toArray();
        unset($data['partner_type_code']);

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('partner_type_code');
    }

    public function test_cannot_update_partner_with_invalid_id(): void
    {
        $model = Partner::factory()->createOne();

        $data = $model->toArray();
        $data['partner_type_code'] = 'Updated Partner';

        $response = $this->putJson("{$this->endpoint}/999999", $data, $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_update_partner_without_authentication(): void
    {
        $model = Partner::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data);
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_update_partner_without_permission(): void
    {
        $model = Partner::factory()->createOne();
        $data = $model->toArray();

        $response = $this->putJson("{$this->endpoint}/{$model->id}", $data, $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_delete_partner_by_id(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getAdminAuthHeaders());
        $response->assertNoContent();

        $this->assertSoftDeleted('partners', [
            'id' => $model->id,
        ]);
    }

    public function test_cannot_delete_partner_by_invalid_id(): void
    {
        $response = $this->deleteJson("{$this->endpoint}/999999", [], $this->getAdminAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_delete_partner_without_authentication(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}");
        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_delete_partner_without_permission(): void
    {
        $model = Partner::factory()->createOne();

        $response = $this->deleteJson("{$this->endpoint}/{$model->id}", [], $this->getCommomUserAuthHeaders());
        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_partners_as_default_excel_file(): void
    {
        Partner::factory(3)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('partner.xlsx', function (PartnerExport $export) {
            return $export->query()->count() === 3
                && $export->headings() === [
                    'ID',
                    'Tipo de parceiro',
                    'Nome',
                    'Apelido',
                    'Tipo de pessoa',
                    'Contribuinte',
                    'Documento',
                    'RG',
                    'Órgão emissor',
                    'Parceiro desde',
                    'Inscrição estadual',
                    'Inscrição municipal',
                    'Inscrição Suframa',
                    'Estado civíl',
                    'CBO profissão',
                    'Gênero',
                    'Data de nascimento',
                    'Nome do pai',
                    'Documento do pai',
                    'Nome da mãe',
                    'Documento da mãe',
                    'Tipo de chave PIX',
                    'Chave PIX',
                    'Observações',
                ];
        });
    }

    public function test_can_export_partners_with_filter(): void
    {
        Partner::factory(3)->create();

        Excel::fake();

        $response = $this->getJson(
            "{$this->endpoint}/export?filters[id][eq]=2",
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('partner.xlsx', function (PartnerExport $export) {
            return $export->query()->pluck('id')->all() === [2];
        });
    }

    public function test_cannot_export_partners_without_authentication(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export");

        $this->assertErrorResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    public function test_cannot_export_partners_without_permission(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export", $this->getCommomUserAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    public function test_can_export_partners_with_explicit_format(): void
    {
        Partner::factory(2)->create();

        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export?format=full", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('partner.xlsx', function (PartnerExport $export) {
            return $export->query()->count() === 2;
        });
    }

    public function test_cannot_export_partners_with_unsupported_extension(): void
    {
        Excel::fake();

        $response = $this->getJson("{$this->endpoint}/export?extension=pdf", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
