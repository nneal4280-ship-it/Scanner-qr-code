<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'first_name', 'last_name', 'created_on'];
    protected function casts(): array { return ['created_on' => 'date']; }
    public function user() { return $this->belongsTo(User::class); }
}
