<?php

namespace App\Models;

use App\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'anomaly_id', 'status', 'title', 'message', 'read_at'];
    protected function casts(): array { return ['status' => AlertStatus::class, 'read_at' => 'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
    public function anomaly() { return $this->belongsTo(Anomaly::class); }
}
