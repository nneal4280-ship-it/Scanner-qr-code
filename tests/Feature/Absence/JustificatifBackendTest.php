<?php

namespace Tests\Feature\Absence;

use App\Enums\AbsenceStatus;
use App\Enums\UserRole;
use App\Models\Justificatif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JustificatifBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_an_absence(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson(route('absences.store'), ['reason' => 'Congé médical', 'starts_on' => '2026-09-28', 'ends_on' => '2026-09-29']);
        $response->assertCreated()->assertJsonPath('status', AbsenceStatus::Pending->value);
    }

    public function test_responsible_can_review_a_team_member_absence(): void
    {
        $responsible = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $employee = User::factory()->create(['supervisor_id' => $responsible->id]);
        $absence = Justificatif::factory()->create(['user_id' => $employee->id]);
        $this->actingAs($responsible)->patchJson(route('absences.review', $absence), ['status' => AbsenceStatus::Approved->value])->assertOk();
        $this->assertDatabaseHas('justificatifs', ['id' => $absence->id, 'status' => AbsenceStatus::Approved->value, 'reviewer_id' => $responsible->id]);
    }
}
