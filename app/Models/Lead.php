<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'assigned_user_id', 'title',
        'stage', 'source', 'notes', 'expected_close_date', 'value', 'lost_reason',
    ];

    protected $casts = ['expected_close_date' => 'date', 'value' => 'decimal:2'];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function quotes() { return $this->hasMany(Quote::class); }
    public function installation() { return $this->hasOne(Installation::class); }
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function siteSurvey() { return $this->hasOne(SiteSurvey::class); }

    public static function stages(): array
    {
        return ['new', 'contacted', 'survey_scheduled', 'quote_sent', 'negotiation', 'won', 'lost', 'junk'];
    }

    public static function stageColors(): array
    {
        return [
            'new'              => '#6366f1',
            'contacted'        => '#3b82f6',
            'survey_scheduled' => '#f59e0b',
            'quote_sent'       => '#8b5cf6',
            'negotiation'      => '#ec4899',
            'won'              => '#10b981',
            'lost'             => '#ef4444',
            'junk'             => '#6b7280',
        ];
    }
}
