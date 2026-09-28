<?php

namespace App\Services;

use App\Enums\AttendanceType;
use App\Enums\AttendanceStatus;
use App\Models\Pointage;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(private readonly QrCodeService $qrCodeService, private readonly GeolocationService $geolocationService) {}

    public function record(User $user, array $data): Pointage
    {
        return DB::transaction(function () use ($user, $data): Pointage {
            $site = Site::query()->whereKey($data['site_id'])->where('is_active', true)->first();
            if (! $site) {
                throw ValidationException::withMessages(['site_id' => 'Le site de pointage est indisponible.']);
            }

            $qrToken = $this->qrCodeService->validateForAttendance($data['qr_token'], $site->id);
            $distance = $this->geolocationService->distanceInMeters($data['latitude'], $data['longitude'], $site);
            if ($distance > $this->geolocationService->radius($site)) {
                throw ValidationException::withMessages(['latitude' => 'La position est hors de la zone autorisée.']);
            }

            $type = AttendanceType::from($data['type']);
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $count = Pointage::query()->where('user_id', $lockedUser->id)->whereDate('occurred_at', today())->count();
            if ($count >= 2) {
                throw ValidationException::withMessages(['type' => 'Les deux pointages du jour ont déjà été enregistrés.']);
            }
            $expectedType = $count === 0 ? AttendanceType::Arrival : AttendanceType::Departure;
            if ($type !== $expectedType) {
                throw ValidationException::withMessages(['type' => 'Le type de pointage ne correspond pas à l’étape actuelle.']);
            }

            $pointage = Pointage::create([
                'user_id' => $lockedUser->id,
                'site_id' => $site->id,
                'qr_token_id' => $qrToken->id,
                'type' => $type,
                'occurred_at' => now(),
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'distance_meters' => $distance,
                'within_geofence' => true,
            ]);
            $lockedUser->update(['attendance_status' => $count === 0 ? AttendanceStatus::Present : AttendanceStatus::Completed]);
            return $pointage;
        });
    }
}
