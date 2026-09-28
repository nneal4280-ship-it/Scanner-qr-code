<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'created_by', 'token_hash', 'token_ciphertext', 'context', 'valid_on', 'expires_at', 'is_active', 'deactivated_at', 'consumed_at'];
    protected function casts(): array { return ['valid_on' => 'date', 'expires_at' => 'datetime', 'is_active' => 'boolean', 'deactivated_at' => 'datetime', 'consumed_at' => 'datetime']; }
    public function site() { return $this->belongsTo(Site::class); }
    public function pointages() { return $this->hasMany(Pointage::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function isUsable(): bool { return $this->is_active && $this->consumed_at === null && $this->expires_at->isFuture(); }
    public function isValidForToday(): bool { return $this->isUsable() && $this->valid_on?->isToday(); }
    public function deactivate(): void { $this->forceFill(['is_active' => false, 'deactivated_at' => now()])->save(); }
}
