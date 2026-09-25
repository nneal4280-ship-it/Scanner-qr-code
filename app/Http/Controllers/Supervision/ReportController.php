<?php

namespace App\Http\Controllers\Supervision;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\StoreReportRequest;
use App\Services\ReportService;
use App\Models\Rapport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request, ReportService $service): JsonResponse
    {
        $report = $service->generate($request->user(), Carbon::parse($request->date('period_start')), Carbon::parse($request->date('period_end')));
        return response()->json($report, 201);
    }

    public function index(Request $request): mixed
    {
        $this->authorize('viewAny', Rapport::class);
        $reports = Rapport::query()->latest('generated_at')->paginate(12);
        return $request->expectsJson() ? response()->json($reports) : response()->view('reports.index', compact('reports'));
    }

    public function validateReport(\Illuminate\Http\Request $request, Rapport $rapport): JsonResponse
    {
        $this->authorize('validate', $rapport);
        $rapport->update(['validated_by' => $request->user()->id, 'validated_at' => now()]);
        return response()->json($rapport->refresh());
    }
}
