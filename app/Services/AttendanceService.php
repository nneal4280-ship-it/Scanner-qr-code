<?php

namespace App\Services;

use App\Enums\AttendanceType;
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

            $qrToken = $this->qrCodeService->consume($data['qr_token'], $site->id);
            $distance = $this->geolocationService->distanceInMeters($data['latitude'], $data['longitude'], $site);
            if ($distance > $site->radius_meters) {
                throw ValidationException::withMessages(['latitude' => 'La position est hors de la zone autorisée.']);
            }

            $type = AttendanceType::from($data['type']);
            $alreadyRecorded = Pointage::query()->where('user_id', $user->id)->where('type', $type->value)->whereDate('occurred_at', today())->exists();
            if ($alreadyRecorded) {
                throw ValidationException::withMessages(['type' => 'Ce type de pointage a déjà été enregistré aujourd’hui.']);
            }

            return Pointage::create([
                'user_id' => $user->id,
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
        });
    }
}
