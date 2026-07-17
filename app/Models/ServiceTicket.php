<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTicket extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'installation_id', 'assigned_user_id',
        'title', 'description', 'priority', 'status', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function installation() { return $this->belongsTo(Installation::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }

    public static function priorities(): array { return ['low', 'medium', 'high', 'urgent']; }
    public static function statuses(): array { return ['open', 'in_progress', 'resolved', 'closed']; }

    public static function priorityColor(string $p): string
    {
        return match($p) {
            'low'    => '#10b981',
            'medium' => '#f59e0b',
            'high'   => '#f97316',
            'urgent' => '#ef4444',
            default  => '#6b7280',
        };
    }
}
