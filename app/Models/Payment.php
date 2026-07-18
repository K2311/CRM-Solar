<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'quote_id', 'amount', 'method', 'reference', 'payment_date', 'notes', 'receipt_file'
    ];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function quote() { return $this->belongsTo(Quote::class); }
}
