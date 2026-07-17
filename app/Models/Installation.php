<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'lead_id', 'quote_id', 'assigned_user_id',
        'status', 'scheduled_date', 'completed_date', 'system_size_kw',
        'panel_brand', 'inverter_brand', 'panel_count', 'notes',
    ];

    protected $casts = ['scheduled_date' => 'date', 'completed_date' => 'date'];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function quote() { return $this->belongsTo(Quote::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function serviceTickets() { return $this->hasMany(ServiceTicket::class); }
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
}
