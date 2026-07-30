<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id', 'name', 'channel',
        'status', 'subject', 'body',
        'segment', 'selected_contacts', 'scheduled_at', 'sent_at', 'sent_count', 'failed_count', 'total_contacts',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime', 
        'sent_at' => 'datetime',
        'selected_contacts' => 'array'
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function contacts() { return $this->hasMany(CampaignContact::class); }

    public static function channels(): array
    {
        return ['whatsapp', 'email'];
    }

    public static function channelIcons(): array
    {
        return [
            'whatsapp'  => 'bi-whatsapp',
            'email'     => 'bi-envelope',
        ];
    }
}
