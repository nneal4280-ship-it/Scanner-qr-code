<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\QrToken;
use App\Models\User;

class QrTokenPolicy
{
    public function create(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel); }
    public function viewAny(User $user): bool { return $user->isRole(UserRole::ResponsablePersonnel); }
    public function update(User $user, QrToken $qrToken): bool { return $user->isRole(UserRole::ResponsablePersonnel) && $qrToken->created_by === $user->id; }
    public function export(User $user, QrToken $qrToken): bool { return $this->update($user, $qrToken); }
}
