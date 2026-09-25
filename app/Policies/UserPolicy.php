<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel, UserRole::ChefCentre, UserRole::Administrateur); }
    public function update(User $user, User $target): bool { return $user->isRole(UserRole::Administrateur) && $user->id !== $target->id; }
}
