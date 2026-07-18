<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanUpgradeRequest extends Model
{
    protected $fillable = [
        'company_id', 'requested_by', 'current_plan', 'requested_plan',
        'payment_proof', 'notes', 'status', 'admin_remarks', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function company() { return $this->belongsTo(Company::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
