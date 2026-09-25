<?php

namespace App\Models;

use App\Enums\AnomalyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'pointage_id', 'type', 'description', 'detected_at', 'resolved'];
    protected function casts(): array { return ['type' => AnomalyType::class, 'detected_at' => 'datetime', 'resolved' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
    public function pointage() { return $this->belongsTo(Pointage::class); }
    public function alerts() { return $this->hasMany(Alert::class); }
}
