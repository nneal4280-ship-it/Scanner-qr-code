<?php

namespace App\Services;

use App\Models\Pointage;
use App\Models\Rapport;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generate(User $generator, Carbon $start, Carbon $end): Rapport
    {
        $query = Pointage::query()->whereBetween('occurred_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
        if ($generator->teamMembers()->exists()) {
            $query->whereIn('user_id', $generator->teamMembers()->pluck('id'));
        }

        $content = [
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'total_pointages' => (clone $query)->count(),
            'arrivals' => (clone $query)->where('type', 'arrival')->count(),
            'departures' => (clone $query)->where('type', 'departure')->count(),
            'users' => (clone $query)->distinct('user_id')->count('user_id'),
        ];

        return DB::transaction(fn () => Rapport::create(['generated_by' => $generator->id, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString(), 'content' => $content, 'generated_at' => now()]));
    }
}
