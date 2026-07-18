<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstInvoice extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'quote_id', 'installation_id', 'invoice_number',
        'invoice_date', 'subtotal', 'discount', 'taxable_value', 'cgst_rate',
        'cgst_amount', 'sgst_rate', 'sgst_amount', 'igst_rate', 'igst_amount',
        'total_gst', 'grand_total', 'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'taxable_value' => 'decimal:2',
        'cgst_rate' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_rate' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_rate' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function quote() { return $this->belongsTo(Quote::class); }
    public function installation() { return $this->belongsTo(Installation::class); }

    public static function generateNumber(int $companyId): string
    {
        $last = static::withoutGlobalScopes()->where('company_id', $companyId)->count();
        return 'INV-' . date('Y') . '-' . str_pad($companyId, 3, '0', STR_PAD_LEFT) . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
