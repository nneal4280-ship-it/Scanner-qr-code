<?php

namespace App\Http\Controllers\Absence;

use App\Enums\AbsenceStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Absence\ReviewJustificatifRequest;
use App\Http\Requests\Absence\StoreJustificatifRequest;
use App\Models\Justificatif;
use App\Services\AbsenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JustificatifController extends Controller
{
    public function index(Request $request): mixed { $user = $request->user(); $query = $user->justificatifs(); if ($user->isRole(UserRole::ResponsablePersonnel)) { $query = Justificatif::query()->whereIn('user_id', $user->teamMembers()->pluck('id')); } elseif ($user->isRole(UserRole::ChefCentre, UserRole::Administrateur)) { $query = Justificatif::query(); } $justificatifs = $query->with(['reviewer', 'user'])->latest()->paginate(20); return $request->expectsJson() ? response()->json($justificatifs) : response()->view('absences.index', compact('justificatifs')); }
    public function store(StoreJustificatifRequest $request, AbsenceService $service): JsonResponse { return response()->json($service->submit($request->user(), $request->safe()->except('file'), $request->file('file')), 201); }
    public function show(Justificatif $justificatif): mixed { $this->authorize('view', $justificatif); return request()->expectsJson() ? response()->json($justificatif->load(['user', 'reviewer'])) : response()->view('absences.show', compact('justificatif')); }
    public function review(ReviewJustificatifRequest $request, Justificatif $justificatif, AbsenceService $service): JsonResponse { return response()->json($service->review($justificatif, $request->user(), AbsenceStatus::from($request->string('status')->toString()), $request->input('review_comment'))); }
}
