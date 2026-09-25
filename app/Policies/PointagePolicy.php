<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Pointage;
use App\Models\User;

class PointagePolicy
{
    public function create(User $user): bool { return $user->isRole(UserRole::Personnel, UserRole::ResponsablePersonnel, UserRole::ChefCentre, UserRole::Administrateur); }
    public function view(User $user, Pointage $pointage): bool { return $pointage->user_id === $user->id || $user->isRole(UserRole::Administrateur) || $pointage->user?->supervisor_id === $user->id; }
}
