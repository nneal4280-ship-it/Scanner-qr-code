<?php

namespace App\Http\Controllers\Supervision;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supervision\SearchPersonnelRequest;
use App\Models\Pointage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index(SearchPersonnelRequest $request): mixed
    {
        $query = User::query()->where('role', 'personnel')->with('profile');
        if ($request->filled('q')) { $query->where(function ($q) use ($request): void { $term = $request->string('q')->toString(); $q->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%'); }); }
        if ($request->user()->role->value === 'responsable_personnel') { $query->where('supervisor_id', $request->user()->id); }
        $personnel = $query->paginate(30);
        return $request->expectsJson() ? response()->json($personnel) : response()->view('supervision.personnel', compact('personnel'));
    }

    public function attendance(Request $request): mixed
    {
        abort_unless($request->user()->can('viewAny', User::class), 403);
        $userIds = $request->user()->teamMembers()->pluck('id');
        $query = Pointage::query()->with('user')->whereIn('user_id', $userIds);
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        $pointages = $query->latest('occurred_at')->paginate(30)->withQueryString();
        return $request->expectsJson() ? response()->json($pointages) : response()->view('supervision.attendance', compact('pointages'));
    }
}
