<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'name', 'email', 'phone', 'address',
        'city', 'state', 'zip', 'source', 'notes', 'status',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function leads() { return $this->hasMany(Lead::class); }
    public function quotes() { return $this->hasMany(Quote::class); }
    public function installations() { return $this->hasMany(Installation::class); }
    public function serviceTickets() { return $this->hasMany(ServiceTicket::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
}
