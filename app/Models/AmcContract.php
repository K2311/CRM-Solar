<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmcContract extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'installation_id', 'start_date', 'expiry_date', 'cost', 'status', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function installation() { return $this->belongsTo(Installation::class); }
}
