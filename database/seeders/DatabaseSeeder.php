<?php

namespace Database\Seeders;

use App\Enums\AbsenceStatus;
use App\Enums\AnomalyType;
use App\Enums\AttendanceType;
use App\Enums\UserRole;
use App\Models\Alert;
use App\Models\Anomaly;
use App\Models\Justificatif;
use App\Models\Pointage;
use App\Models\Profile;
use App\Models\Rapport;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $employee = User::updateOrCreate([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ], ['password' => 'password', 'role' => UserRole::Personnel]);
        $responsible = User::updateOrCreate(['email' => 'responsable@example.com'], ['name' => 'Responsable Test', 'password' => 'password', 'role' => UserRole::ResponsablePersonnel]);
        $admin = User::updateOrCreate(['email' => 'admin@example.com'], ['name' => 'Administrateur Test', 'password' => 'password', 'role' => UserRole::Administrateur]);
        $employee->update(['supervisor_id' => $responsible->id]);

        Profile::updateOrCreate(['user_id' => $employee->id], ['first_name' => 'Test', 'last_name' => 'User', 'created_on' => today()]);
        Profile::updateOrCreate(['user_id' => $responsible->id], ['first_name' => 'Responsable', 'last_name' => 'Test', 'created_on' => today()]);

        $site = Site::updateOrCreate(['name' => 'Centre principal'], ['latitude' => 3.8480, 'longitude' => 11.5021, 'radius_meters' => 250, 'is_active' => true]);
        $arrival = Pointage::firstOrCreate(['user_id' => $employee->id, 'type' => AttendanceType::Arrival, 'occurred_at' => Carbon::today()->setTime(8, 0)], ['site_id' => $site->id, 'latitude' => $site->latitude, 'longitude' => $site->longitude, 'distance_meters' => 0, 'within_geofence' => true]);
        Pointage::firstOrCreate(['user_id' => $employee->id, 'type' => AttendanceType::Departure, 'occurred_at' => Carbon::today()->setTime(17, 0)], ['site_id' => $site->id, 'latitude' => $site->latitude, 'longitude' => $site->longitude, 'distance_meters' => 0, 'within_geofence' => true]);

        $absence = Justificatif::updateOrCreate(['user_id' => $employee->id, 'starts_on' => today()->addDay(), 'ends_on' => today()->addDays(2)], ['reason' => 'Justificatif de démonstration', 'status' => AbsenceStatus::Approved, 'reviewer_id' => $responsible->id, 'submitted_at' => now(), 'reviewed_at' => now()]);
        $report = Rapport::updateOrCreate(['generated_by' => $responsible->id, 'period_start' => today()->subDay(), 'period_end' => today()], ['content' => ['total_pointages' => 2, 'arrivals' => 1, 'departures' => 1], 'generated_at' => now()]);
        $anomaly = Anomaly::updateOrCreate(['user_id' => $employee->id, 'type' => AnomalyType::RepeatedLate], ['pointage_id' => $arrival->id, 'description' => 'Exemple d’anomalie détectée', 'detected_at' => now(), 'resolved' => false]);
        Alert::updateOrCreate(['user_id' => $responsible->id, 'anomaly_id' => $anomaly->id], ['title' => 'Anomalie de présence', 'message' => $anomaly->description]);

        unset($admin, $absence, $report);
    }
}
