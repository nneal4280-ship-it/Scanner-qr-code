<?php

namespace App\Models;

use App\Enums\AttendanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;
    protected $table = 'pointages';
    protected $fillable = ['user_id', 'site_id', 'qr_token_id', 'type', 'occurred_at', 'latitude', 'longitude', 'accuracy_meters', 'distance_meters', 'within_geofence'];
    protected function casts(): array { return ['type' => AttendanceType::class, 'occurred_at' => 'datetime', 'latitude' => 'float', 'longitude' => 'float', 'accuracy_meters' => 'float', 'distance_meters' => 'float', 'within_geofence' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
    public function site() { return $this->belongsTo(Site::class); }
    public function qrToken() { return $this->belongsTo(QrToken::class); }
    public function anomalies() { return $this->hasMany(Anomaly::class); }
}
