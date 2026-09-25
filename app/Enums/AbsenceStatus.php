<?php

namespace App\Enums;

enum AbsenceStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
