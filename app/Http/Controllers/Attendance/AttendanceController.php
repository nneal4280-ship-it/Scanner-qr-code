<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(StoreAttendanceRequest $request, AttendanceService $service): JsonResponse
    {
        $pointage = $service->record($request->user(), $request->validated());
        return response()->json($pointage->load('site'), 201);
    }

    public function index(Request $request): mixed
    {
        if (! $request->expectsJson()) {
            return response()->view('attendance.history', ['pointages' => $request->user()->pointages()->latest('occurred_at')->paginate(20)]);
        }
        return response()->json($request->user()->pointages()->latest('occurred_at')->paginate(30));
    }
}
