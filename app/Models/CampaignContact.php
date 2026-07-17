<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignContact extends Model
{
    protected $fillable = [
        'campaign_id', 'contact_type', 'contact_id', 'name', 'phone', 'email',
        'status', 'sent_at', 'error_message',
    ];

    protected $casts = ['sent_at' => 'datetime'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function contact() { return $this->morphTo(); }
}
