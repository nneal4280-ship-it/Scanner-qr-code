<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case NotMarked = 'non_pointe';
    case Present = 'present';
    case Completed = 'terminee';

    public function label(): string
    {
        return match ($this) {
            self::NotMarked => 'Non pointé',
            self::Present => 'Présent',
            self::Completed => 'Terminée',
        };
    }
}
