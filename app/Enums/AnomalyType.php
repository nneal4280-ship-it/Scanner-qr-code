<?php

namespace App\Enums;

enum AnomalyType: string
{
    case RepeatedLate = 'repeated_late';
    case InconsistentAttendance = 'inconsistent_attendance';
    case OutOfGeofence = 'out_of_geofence';
}
