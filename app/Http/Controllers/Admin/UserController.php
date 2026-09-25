<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function edit(User $user): mixed { $this->authorize('update', $user); return response()->view('admin.user-edit', compact('user')); }
    public function index(Request $request): mixed { abort_unless($request->user()->isRole(UserRole::Administrateur), 403); $users = User::query()->with('profile')->latest()->paginate(30); return $request->expectsJson() ? response()->json($users) : response()->view('admin.users', compact('users')); }
    public function update(UpdateUserRequest $request, User $user): mixed { $user->update($request->validated()); return $request->expectsJson() ? response()->json($user->refresh()) : redirect()->route('admin.users.edit', $user)->with('status', 'Utilisateur mis à jour.'); }
}
