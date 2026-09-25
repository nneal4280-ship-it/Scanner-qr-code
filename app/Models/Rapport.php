<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;
    protected $table = 'rapports';
    protected $fillable = ['generated_by', 'period_start', 'period_end', 'content', 'generated_at', 'validated_at', 'validated_by'];
    protected function casts(): array { return ['period_start' => 'date', 'period_end' => 'date', 'content' => 'array', 'generated_at' => 'datetime', 'validated_at' => 'datetime']; }
    public function generator() { return $this->belongsTo(User::class, 'generated_by'); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }
}
