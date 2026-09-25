<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'token_hash', 'context', 'expires_at', 'consumed_at'];
    protected function casts(): array { return ['expires_at' => 'datetime', 'consumed_at' => 'datetime']; }
    public function site() { return $this->belongsTo(Site::class); }
    public function pointages() { return $this->hasMany(Pointage::class); }
    public function isUsable(): bool { return $this->consumed_at === null && $this->expires_at->isFuture(); }
}
