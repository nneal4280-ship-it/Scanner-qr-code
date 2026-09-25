<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\QrToken;
use App\Models\User;

class QrTokenPolicy
{
    public function create(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel, UserRole::ChefCentre, UserRole::Administrateur); }
}
