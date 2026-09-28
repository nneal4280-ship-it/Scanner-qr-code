<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function store(StoreAttendanceRequest $request, AttendanceService $service): mixed
    {
        $pointage = $service->record($request->user(), $request->validated());
        $pointage->load('site', 'user');
        if (! $request->expectsJson()) {
            return redirect()->route('attendance.history')->with([
                'attendance_success' => 'Pointage effectué avec succès.',
                'attendance_time' => $pointage->occurred_at->format('H:i'),
                'attendance_type' => $pointage->type->value === 'arrival' ? 'Entrée' : 'Sortie',
                'attendance_status' => $pointage->user->attendance_status->label(),
            ]);
        }
        return response()->json(['pointage' => $pointage, 'status' => $pointage->user->attendance_status->value, 'status_label' => $pointage->user->attendance_status->label()], 201);
    }

    public function index(Request $request): mixed
    {
        $period = $request->string('period', 'month')->toString();
        if (! in_array($period, ['month', 'week', 'all'], true)) {
            $period = 'month';
        }
        $query = $request->user()->pointages()->latest('occurred_at');
        if ($period === 'month') {
            $query->whereBetween('occurred_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($period === 'week') {
            $query->whereBetween('occurred_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        if (! $request->expectsJson()) {
            return response()->view('attendance.history', ['pointages' => $query->paginate(20)->withQueryString(), 'period' => $period]);
        }
        return response()->json($query->paginate(30)->withQueryString());
    }
}
