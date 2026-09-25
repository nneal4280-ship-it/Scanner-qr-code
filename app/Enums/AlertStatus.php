<?php

namespace App\Enums;

enum AlertStatus: string
{
    case Unread = 'unread';
    case Read = 'read';
    case Resolved = 'resolved';
}
