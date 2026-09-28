<?php

namespace Tests\Integration;

use App\Enums\AttendanceType;
use App\Enums\UserRole;
use App\Models\Pointage;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancePointageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_personnel_can_make_an_arrival_and_departure_pointage_with_the_daily_qr(): void
    {
        $responsable = User::factory()->create(['role' => UserRole::ResponsablePersonnel]);
        $personnel = User::factory()->create(['role' => UserRole::Personnel]);
        $site = Site::factory()->create([
            'latitude' => 4.0503060,
            'longitude' => 9.6940653,
            'radius_meters' => 250,
        ]);

        $qrResponse = $this->actingAs($responsable)->postJson(route('attendance.qr-tokens.store'), [
            'site_id' => $site->id,
        ]);

        $qrResponse->assertCreated();
        $qrToken = $qrResponse->json('token');

        $payload = [
            'site_id' => $site->id,
            'qr_token' => $qrToken,
            'type' => AttendanceType::Arrival->value,
            'latitude' => 4.0503060,
            'longitude' => 9.6940653,
        ];

        $this->actingAs($personnel)
            ->postJson(route('attendance.store'), $payload)
            ->assertCreated()
            ->assertJsonPath('status', 'present');

        $payload['type'] = AttendanceType::Departure->value;

        $this->actingAs($personnel)
            ->postJson(route('attendance.store'), $payload)
            ->assertCreated()
            ->assertJsonPath('status', 'terminee');

        $this->assertSame(2, Pointage::where('user_id', $personnel->id)->count());
        $this->assertDatabaseHas('pointages', [
            'user_id' => $personnel->id,
            'site_id' => $site->id,
            'type' => AttendanceType::Arrival->value,
            'latitude' => 4.0503060,
            'longitude' => 9.6940653,
            'within_geofence' => true,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $personnel->id,
            'attendance_status' => 'terminee',
        ]);
    }
}
