<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'content',
        'media_urls',
        'platform',
        'post_type',
        'status',
        'scheduled_at',
        'provider_post_id',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'media_urls' => 'array',
            'scheduled_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
