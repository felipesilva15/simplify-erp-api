<?php

namespace Tests\Feature\Security;

use App\Core\Enums\ActivityActionEnum;
use App\Core\Models\ActivityLog;
use App\Modules\Security\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_can_list_activity_logs_of_a_user(): void
    {
        $user = User::factory()->create();

        $this->createActivityLog($user, ActivityActionEnum::Created);
        $this->createActivityLog($user, ActivityActionEnum::Updated);

        $response = $this->getJson("/api/security/users/{$user->id}/activity-logs", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK);
        $this->assertApiResponseStructureForListing($response);
        $response->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'origin_type',
                        'origin_id',
                        'action',
                        'action_label',
                        'description',
                        'created_at'
                    ]
                ]
            ]);

        $actions = collect($response->json('data'))->pluck('action')->all();

        $this->assertEqualsCanonicalizing(['created', 'updated'], $actions);
        $this->assertSame('user', $response->json('data.0.origin_type'));
        $this->assertSame((string) $user->id, $response->json('data.0.origin_id'));
    }

    public function test_activity_logs_are_filtered_by_resource(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $this->createActivityLog($user, ActivityActionEnum::Created);
        $this->createActivityLog($anotherUser, ActivityActionEnum::Deleted);

        $response = $this->getJson("/api/security/users/{$user->id}/activity-logs", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.action', 'created')
            ->assertJsonPath('data.0.origin_id', (string) $user->id);
    }

    public function test_cannot_list_activity_logs_of_nonexistent_user(): void
    {
        $response = $this->getJson("/api/security/users/999999/activity-logs", $this->getAdminAuthHeaders());

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_cannot_list_activity_logs_as_common_user(): void
    {
        $user = User::factory()->create();

        $this->createActivityLog($user, ActivityActionEnum::Created);

        $response = $this->getJson("/api/security/users/{$user->id}/activity-logs", $this->getCommomUserAuthHeaders());

        $this->assertErrorResponse($response, Response::HTTP_FORBIDDEN);
    }

    private function createActivityLog(User $origin, ActivityActionEnum $action): ActivityLog
    {
        return ActivityLog::create([
            'origin_type' => 'user',
            'origin_id'   => (string) $origin->id,
            'action'      => $action,
            'user_id'     => $this->adminAuthUser->id,
            'description' => 'Registro criado',
        ]);
    }
}