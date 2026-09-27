<?php

namespace Tests\Feature\ThirdParty;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Models\ActivityLog;
use App\Modules\ThirdParty\Enums\PersonTypeEnum;
use App\Modules\ThirdParty\Enums\TaxpayerTypeEnum;
use App\Modules\ThirdParty\Models\Contact;
use App\Modules\ThirdParty\Models\Partner;
use App\Modules\ThirdParty\Models\PartnerType;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PartnerContactTest extends TestCase
{
    protected string $endpoint = '/api/third-party/partners';

    private PartnerType $partnerType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->partnerType = PartnerType::factory()->createOne();
    }

    private function partnerPayload(array $overrides = []): array
    {
        return array_merge([
            'partner_type_code' => $this->partnerType->code,
            'name' => 'Empresa Exemplo LTDA',
            'trade_name' => 'Empresa Exemplo',
            'person_type' => PersonTypeEnum::Company->value,
            'taxpayer_type' => TaxpayerTypeEnum::NonTaxpayer->value,
            'document_number' => (string) fake()->unique()->numerify('#############'),
        ], $overrides);
    }

    private function contactPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Roberto',
            'department' => 'TI',
            'email' => fake()->unique()->safeEmail(),
            'main' => false,
        ], $overrides);
    }

    private function unchangedPayloadFor(Contact $contact): array
    {
        return [
            'id' => $contact->id,
            'name' => $contact->name,
            'department' => $contact->department,
            'email' => $contact->email,
            'mobile' => $contact->mobile,
            'phone' => $contact->phone,
            'main' => (bool) $contact->main,
        ];
    }

    public function test_can_create_partner_with_contacts_in_a_single_request(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                $this->contactPayload(['name' => 'Roberto', 'main' => true]),
                $this->contactPayload(['name' => 'Ana', 'email' => 'ana@exemplo.com.br']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('data.contacts.0.name', 'Roberto')
            ->assertJsonPath('data.contacts.0.main', true)
            ->assertJsonPath('data.contacts.1.name', 'Ana')
            ->assertJsonCount(2, 'data.contacts');

        $this->assertDatabaseHas('contacts', [
            'partner_id' => $response->json('data.id'),
            'name' => 'Roberto',
        ]);

        $this->assertDatabaseHas('contacts', [
            'partner_id' => $response->json('data.id'),
            'name' => 'Ana',
        ]);
    }

    public function test_create_returns_summary_of_children_changes(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [$this->contactPayload()],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('meta.children.contacts.created', 1)
            ->assertJsonPath('meta.children.contacts.updated', 0)
            ->assertJsonPath('meta.children.contacts.deleted', 0);
    }

    public function test_create_without_contacts_key_does_not_add_meta(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload(), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonCount(0, 'data.contacts');

        $this->assertNull($response->json('meta'));
    }

    public function test_update_creates_updates_and_deletes_children(): void
    {
        $partner = $this->createPartnerWithThreeContacts();

        [$keep, $change, $remove] = $partner->contacts()->orderBy('id')->get()->all();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                $this->unchangedPayloadFor($keep),
                ['id' => $change->id, ...$this->unchangedPayloadFor($change), 'name' => 'Alterado'],
                $this->contactPayload(['name' => 'Novo contato']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.contacts.created', 1)
            ->assertJsonPath('meta.children.contacts.updated', 1)
            ->assertJsonPath('meta.children.contacts.deleted', 1)
            ->assertJsonCount(3, 'data.contacts');

        $this->assertDatabaseHas('contacts', ['id' => $keep->id, 'name' => 'Contato A']);
        $this->assertDatabaseHas('contacts', ['id' => $change->id, 'name' => 'Alterado']);
        $this->assertSoftDeleted('contacts', ['id' => $remove->id]);
        $this->assertDatabaseHas('contacts', ['name' => 'Novo contato']);
    }

    public function test_update_without_contacts_key_preserves_children(): void
    {
        $partner = $this->createPartnerWithThreeContacts();

        $response = $this->putJson(
            "{$this->endpoint}/{$partner->id}",
            $this->partnerPayload(['name' => 'Empresa Renomeada']),
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('data.name', 'Empresa Renomeada')
            ->assertJsonCount(3, 'data.contacts');

        $this->assertSame(3, Contact::where('partner_id', $partner->id)->count());
        $this->assertNull($response->json('meta'));
    }

    public function test_update_with_empty_contacts_list_removes_all(): void
    {
        $partner = $this->createPartnerWithThreeContacts();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.contacts.deleted', 3)
            ->assertJsonCount(0, 'data.contacts');

        $this->assertSame(0, Contact::where('partner_id', $partner->id)->count());
        $this->assertSame(3, Contact::onlyTrashed()->where('partner_id', $partner->id)->count());
    }

    public function test_unchanged_child_is_not_written_again(): void
    {
        $partner = $this->createPartnerWithThreeContacts();
        $contact = $partner->contacts()->orderBy('id')->first();
        $originalUpdatedAt = $contact->updated_at;

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                $this->unchangedPayloadFor($contact),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.contacts.updated', 0)
            ->assertJsonPath('meta.children.contacts.deleted', 2);

        $this->assertTrue(
            $originalUpdatedAt->equalTo($contact->fresh()->updated_at),
            'Item sem alteração não deve ser gravado novamente.'
        );
    }

    public function test_removed_child_is_restored_when_sent_again(): void
    {
        $partner = $this->createPartnerWithThreeContacts();
        $contact = $partner->contacts()->orderBy('id')->first();

        $contact->delete();
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                $this->unchangedPayloadFor($contact),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('meta.children.contacts.created', 0)
            ->assertJsonPath('meta.children.contacts.deleted', 2);

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'deleted_at' => null]);
    }

    public function test_cannot_assign_child_from_another_header(): void
    {
        $partner  = $this->createPartnerWithThreeContacts();
        $other    = $this->createPartnerWithThreeContacts();
        $foreign  = $other->contacts()->first();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                ['id' => $foreign->id, ...$this->contactPayload()],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('contacts.0.id');

        $this->assertDatabaseHas('contacts', [
            'id' => $foreign->id,
            'partner_id' => $other->id,
        ]);
    }

    public function test_whole_sync_is_rolled_back_when_a_child_is_invalid(): void
    {
        $partner = $this->createPartnerWithThreeContacts();

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'name' => 'Nome que deve ser desfeito',
            'contacts' => [
                $this->contactPayload(['name' => 'Contato válido']),
                ['name' => 'Contato sem meio de contato', 'email' => null],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);

        $partner->refresh();
        $this->assertNotSame('Nome que deve ser desfeito', $partner->name);
        $this->assertSame(3, Contact::where('partner_id', $partner->id)->count());
        $this->assertSame(0, Contact::where('name', 'Contato válido')->count());
    }

    public function test_cannot_create_contact_without_any_contact_channel(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                ['name' => 'Sem contato', 'email' => null, 'mobile' => null, 'phone' => null],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('contacts.0.email');

        $this->assertSame(0, Contact::count());
        $this->assertSame(0, Partner::count());
    }

    public function test_cannot_mark_more_than_one_contact_as_main(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                $this->contactPayload(['name' => 'A', 'main' => true]),
                $this->contactPayload(['name' => 'B', 'main' => true]),
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['contacts.0.main', 'contacts.1.main']);
        $this->assertSame(0, Contact::count());
    }

    public function test_single_main_contact_is_accepted(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                $this->contactPayload(['name' => 'A', 'main' => true]),
                $this->contactPayload(['name' => 'B', 'main' => false]),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('contacts', ['name' => 'A', 'main' => true]);
    }

    public function test_main_rule_also_sees_main_persisted_on_existing_children(): void
    {
        $partner = $this->createPartnerWithThreeContacts();
        [$first, $second] = $partner->contacts()->orderBy('id')->get()->all();

        $first->update(['main' => true]);

        $withoutMain = $this->unchangedPayloadFor($first);
        unset($withoutMain['main']);

        $response = $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                $withoutMain,
                ['id' => $second->id, ...$this->unchangedPayloadFor($second), 'main' => true],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('contacts.1.main');
    }

    public function test_cannot_create_contact_with_invalid_shape(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                ['name' => 'Contato', 'email' => 'nao-e-email'],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('contacts.0.email');
    }

    public function test_cannot_create_contact_without_name(): void
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                ['email' => 'sem-nome@exemplo.com.br'],
            ],
        ]), $this->getAdminAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('contacts.0.name');
    }

    public function test_each_child_operation_is_logged(): void
    {
        $partner = $this->createPartnerWithThreeContacts();
        [$keep, $change, $remove] = $partner->contacts()->orderBy('id')->get()->all();

        $logsBefore = ActivityLog::query()
            ->where('origin_type', Contact::morphAlias())
            ->where('origin_id', (string) $keep->id)
            ->count();

        $this->putJson("{$this->endpoint}/{$partner->id}", $this->partnerPayload([
            'contacts' => [
                $this->unchangedPayloadFor($keep),
                ['id' => $change->id, ...$this->unchangedPayloadFor($change), 'name' => 'Alterado'],
                $this->contactPayload(['name' => 'Criado agora']),
            ],
        ]), $this->getAdminAuthHeaders())->assertStatus(Response::HTTP_OK);

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Contact::morphAlias())
                ->where('origin_id', (string) $change->id)
                ->where('action', ActivityActionEnum::Updated->value)
                ->count(),
            'Contato alterado deve ter log de alteração.'
        );

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Contact::morphAlias())
                ->where('origin_id', (string) $remove->id)
                ->where('action', ActivityActionEnum::Deleted->value)
                ->count(),
            'Contato removido deve ter log de exclusão.'
        );

        $created = Contact::where('name', 'Criado agora')->firstOrFail();
        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('origin_type', Contact::morphAlias())
                ->where('origin_id', (string) $created->id)
                ->where('action', ActivityActionEnum::Created->value)
                ->count(),
            'Contato criado deve ter log de criação.'
        );

        $this->assertSame(
            $logsBefore,
            ActivityLog::query()
                ->where('origin_type', Contact::morphAlias())
                ->where('origin_id', (string) $keep->id)
                ->count(),
            'Contato sem alteração não deve gerar novo log.'
        );
    }

    public function test_child_history_is_queryable_by_morph_alias(): void
    {
        $partner = $this->createPartnerWithThreeContacts();
        $contact = $partner->contacts()->orderBy('id')->first();

        $this->assertSame('contact', Contact::morphAlias());
        $this->assertSame(
            'contact',
            \Illuminate\Database\Eloquent\Relations\Relation::getMorphAlias(Contact::class),
            'O model de item precisa estar no morph map para o histórico ser consultável.'
        );

        $response = $this->getJson(
            "/api/third-party/partners/{$partner->id}/activity-logs",
            $this->getAdminAuthHeaders()
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_loads_contacts_without_extra_queries(): void
    {
        Partner::factory(3)->create(['partner_type_code' => $this->partnerType->code]);
        $this->createPartnerWithThreeContacts();

        $queries = $this->recordContactQueriesFor(
            fn () => $this->getJson($this->endpoint, $this->getAdminAuthHeaders())
        );

        $this->assertCount(1, $queries, 'A listagem deve buscar os contatos uma única vez.');
    }

    public function test_show_single_partner_loads_contacts_without_extra_queries(): void
    {
        $partner = $this->createPartnerWithThreeContacts();

        $queries = $this->recordContactQueriesFor(
            fn () => $this->getJson("{$this->endpoint}/{$partner->id}", $this->getAdminAuthHeaders())
        );

        $this->assertCount(1, $queries, 'O detalhe deve buscar o parceiro e os contatos juntos.');
    }

    private function recordContactQueriesFor(callable $request): array
    {
        $recorded = [];

        DB::listen(function ($query) use (&$recorded): void {
            if (str_contains($query->sql, '"contacts"')) {
                $recorded[] = $query->sql;
            }
        });

        $request();

        return $recorded;
    }

    private function createPartnerWithThreeContacts(): Partner
    {
        $response = $this->postJson($this->endpoint, $this->partnerPayload([
            'contacts' => [
                $this->contactPayload(['name' => 'Contato A']),
                $this->contactPayload(['name' => 'Contato B', 'email' => 'b@exemplo.com.br']),
                $this->contactPayload(['name' => 'Contato C', 'email' => 'c@exemplo.com.br']),
            ],
        ]), $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_CREATED);

        return Partner::findOrFail($response->json('data.id'));
    }
}
