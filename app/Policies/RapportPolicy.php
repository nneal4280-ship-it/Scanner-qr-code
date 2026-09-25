<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Rapport;
use App\Models\User;

class RapportPolicy
{
    public function viewAny(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel, UserRole::ChefCentre, UserRole::Administrateur); }
    public function create(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel, UserRole::ChefCentre, UserRole::Administrateur); }
    public function validate(User $user, Rapport $rapport): bool { return $user->isRole(UserRole::ChefCentre, UserRole::Administrateur); }
}
