<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'customer_id', 'lead_id', 'quote_number', 'status',
        'valid_until', 'notes', 'subtotal', 'discount', 'tax_rate', 'tax_amount', 'total',
    ];

    protected $casts = ['valid_until' => 'date'];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function items() { return $this->hasMany(QuoteItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $discount = $this->discount ?? 0;
        $taxable = $subtotal - $discount;
        $tax = $taxable * ($this->tax_rate / 100);
        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $tax,
            'total'      => $taxable + $tax,
        ]);
    }

    public static function generateNumber(int $companyId): string
    {
        $last = static::withoutGlobalScopes()->where('company_id', $companyId)->count();
        return 'QT-' . str_pad($companyId, 3, '0', STR_PAD_LEFT) . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
