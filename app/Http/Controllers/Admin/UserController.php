<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request): mixed
    {
        $this->authorize('create', User::class);

        return response()->view('admin.user-create', [
            'roles' => UserRole::cases(),
            'supervisors' => User::query()->whereIn('role', [UserRole::ResponsablePersonnel, UserRole::ChefCentre])->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): mixed
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'supervisor_id' => $validated['supervisor_id'] ?? null,
            ]);

            $user->profile()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'position' => $validated['position'],
                'department' => $validated['department'],
                'created_on' => today(),
            ]);

            return $user;
        });

        if ($request->expectsJson()) {
            return response()->json($user->load('profile'), 201);
        }

        return redirect()->route('admin.users.index')->with('status', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): mixed { $this->authorize('update', $user); return response()->view('admin.user-edit', compact('user')); }
    public function index(Request $request): mixed { abort_unless($request->user()->isRole(UserRole::Administrateur), 403); $users = User::query()->with('profile')->latest()->paginate(30); return $request->expectsJson() ? response()->json($users) : response()->view('admin.users', compact('users')); }
    public function update(UpdateUserRequest $request, User $user): mixed { $user->update($request->validated()); return $request->expectsJson() ? response()->json($user->refresh()) : redirect()->route('admin.users.edit', $user)->with('status', 'Utilisateur mis à jour.'); }
}
