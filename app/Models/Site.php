<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'latitude', 'longitude', 'radius_meters', 'is_active'];
    protected function casts(): array { return ['latitude' => 'float', 'longitude' => 'float', 'radius_meters' => 'integer', 'is_active' => 'boolean']; }
    public function pointages() { return $this->hasMany(Pointage::class); }
    public function qrTokens() { return $this->hasMany(QrToken::class); }
}
