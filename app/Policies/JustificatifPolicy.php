<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Justificatif;
use App\Models\User;

class JustificatifPolicy
{
    public function create(User $user): bool { return true; }
    public function view(User $user, Justificatif $justificatif): bool { return $justificatif->user_id === $user->id || $this->canReview($user, $justificatif); }
    public function review(User $user, Justificatif $justificatif): bool { return $this->canReview($user, $justificatif); }
    private function canReview(User $user, Justificatif $justificatif): bool { return $user->isRole(UserRole::Administrateur, UserRole::ChefCentre) || ($user->isRole(UserRole::ResponsablePersonnel) && $justificatif->user?->supervisor_id === $user->id); }
}
