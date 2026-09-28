<?php

namespace Tests\Feature\Attendance;

use App\Enums\AttendanceType;
use App\Enums\UserRole;
use App\Models\Site;
use App\Models\Pointage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_responsable_can_issue_and_personnel_can_use_a_daily_qr_pointage(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 4.050306, 'longitude' => 9.6940653, 'radius_meters' => 250]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->assertCreated()->json('token');
        $this->actingAs(User::factory()->create(['role' => UserRole::Personnel]))->postJson(route('attendance.store'), ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 4.050306, 'longitude' => 9.6940653])->assertCreated();
        $this->assertDatabaseHas('pointages', ['type' => AttendanceType::Arrival->value, 'qr_token_id' => \App\Models\QrToken::query()->value('id')]);
    }

    public function test_same_daily_qr_supports_entry_and_exit_but_not_duplicate_type(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 4.050306, 'longitude' => 9.6940653, 'radius_meters' => 250]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $payload = ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 4.050306, 'longitude' => 9.6940653];
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertCreated();
        $payload['type'] = AttendanceType::Departure->value;
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertCreated();
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertStatus(422)->assertJsonValidationErrors('type');
    }

    public function test_pointage_outside_site_is_rejected(): void
    {
        $user = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 4.050306, 'longitude' => 9.6940653, 'radius_meters' => 50]);
        $qr = $this->actingAs($user)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $this->actingAs($user)->postJson(route('attendance.store'), ['site_id' => $site->id, 'qr_token' => $qr, 'type' => AttendanceType::Arrival->value, 'latitude' => 4.0600, 'longitude' => 9.6940653])->assertStatus(422)->assertJsonValidationErrors('latitude');
    }

    public function test_attendance_updates_status_and_rejects_a_third_pointage(): void
    {
        $user = User::factory()->create(['role' => UserRole::Personnel]);
        $responsable = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 4.050306, 'longitude' => 9.6940653]);
        $qr = $this->actingAs($responsable)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $payload = ['site_id' => $site->id, 'qr_token' => $qr, 'type' => 'arrival', 'latitude' => 4.050306, 'longitude' => 9.6940653];

        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertCreated();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'attendance_status' => 'present']);
        $payload['type'] = 'departure';
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertCreated();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'attendance_status' => 'terminee']);
        $payload['type'] = 'arrival';
        $this->actingAs($user)->postJson(route('attendance.store'), $payload)->assertStatus(422)->assertJsonValidationErrors('type');
        $this->assertSame(2, $user->pointages()->count());
    }

    public function test_only_responsable_can_create_a_qr(): void
    {
        $site = Site::factory()->create();
        $this->actingAs(User::factory()->create(['role' => UserRole::Personnel]))
            ->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])
            ->assertForbidden();
    }

    public function test_responsable_can_export_the_active_qr_as_a_docx(): void
    {
        $responsable = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $site = Site::factory()->create(['latitude' => 4.050306, 'longitude' => 9.6940653]);
        $token = $this->actingAs($responsable)->postJson(route('attendance.qr-tokens.store'), ['site_id' => $site->id])->json('token');
        $qr = \App\Models\QrToken::query()->where('site_id', $site->id)->firstOrFail();
        $png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->actingAs($responsable)->post(route('attendance.qr-tokens.export', $qr), [
            'qr_token' => $token,
            'qr_image' => 'data:image/png;base64,'.$png,
        ]);

        $response->assertOk()->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }

    public function test_dashboard_derives_completed_status_from_today_pointages(): void
    {
        $user = User::factory()->create(['role' => UserRole::Personnel, 'attendance_status' => 'non_pointe']);
        $site = Site::factory()->create();
        Pointage::factory()->create(['user_id' => $user->id, 'site_id' => $site->id, 'type' => AttendanceType::Arrival, 'occurred_at' => now()]);
        Pointage::factory()->create(['user_id' => $user->id, 'site_id' => $site->id, 'type' => AttendanceType::Departure, 'occurred_at' => now()->addMinute()]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Terminée')
            ->assertSee('Pointages aujourd’hui');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'attendance_status' => 'terminee']);
    }
}
