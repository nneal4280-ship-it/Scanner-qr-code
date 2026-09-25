<?php

namespace App\Models;

use App\Enums\AbsenceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justificatif extends Model
{
    use HasFactory;
    protected $table = 'justificatifs';
    protected $fillable = ['user_id', 'reviewer_id', 'status', 'reason', 'starts_on', 'ends_on', 'submitted_at', 'reviewed_at', 'review_comment', 'file_path', 'file_original_name', 'file_mime_type', 'file_size'];
    protected function casts(): array { return ['status' => AbsenceStatus::class, 'starts_on' => 'date', 'ends_on' => 'date', 'submitted_at' => 'datetime', 'reviewed_at' => 'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
}
