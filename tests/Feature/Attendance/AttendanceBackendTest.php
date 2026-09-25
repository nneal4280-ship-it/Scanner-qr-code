<?php

namespace Tests\Feature\Attendance;

use App\Enums\AttendanceType;
use App\Enums\UserRole;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_personnel_can_issue_and_consume_a_qr_pointage(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 3.848, 'longitude' => 11.502, 'radius_meters' => 100]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->assertCreated()->json('token');
        $this->actingAs($user)->postJson(route('attendance.store'), ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 3.848, 'longitude' => 11.502])->assertCreated();
        $this->assertDatabaseHas('pointages', ['user_id' => $user->id, 'type' => AttendanceType::Arrival->value]);
    }

    public function test_qr_token_cannot_be_replayed(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 3.848, 'longitude' => 11.502]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $payload = ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 3.848, 'longitude' => 11.502];
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertCreated();
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertStatus(422)->assertJsonValidationErrors('qr_token');
    }

    public function test_pointage_outside_site_is_rejected(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 3.848, 'longitude' => 11.502, 'radius_meters' => 50]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $this->actingAs($user)->postJson(route('attendance.store'), ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 4.0, 'longitude' => 11.5])->assertStatus(422)->assertJsonValidationErrors('latitude');
    }
}
