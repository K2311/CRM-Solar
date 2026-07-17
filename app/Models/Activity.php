<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id', 'subject_type', 'subject_id', 'user_id',
        'type', 'title', 'description', 'due_at', 'completed_at',
    ];

    protected $casts = ['due_at' => 'datetime', 'completed_at' => 'datetime'];

    public function subject() { return $this->morphTo(); }
    public function user() { return $this->belongsTo(User::class); }

    public static function types(): array
    {
        return ['note', 'call', 'email', 'meeting', 'task'];
    }
}
