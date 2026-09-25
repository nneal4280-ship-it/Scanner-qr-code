<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\GenerateQrTokenRequest;
use App\Models\Site;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;

class QrTokenController extends Controller
{
    public function store(GenerateQrTokenRequest $request, QrCodeService $service): JsonResponse
    {
        return response()->json($service->issue(Site::findOrFail($request->integer('site_id'))), 201);
    }
}
