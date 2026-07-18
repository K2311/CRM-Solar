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
        'advance_milestone_pct', 'delivery_milestone_pct', 'commissioning_milestone_pct',
        'has_subsidy', 'central_subsidy', 'state_subsidy', 'net_cost',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'has_subsidy' => 'boolean',
        'advance_milestone_pct' => 'decimal:2',
        'delivery_milestone_pct' => 'decimal:2',
        'commissioning_milestone_pct' => 'decimal:2',
        'central_subsidy' => 'decimal:2',
        'state_subsidy' => 'decimal:2',
        'net_cost' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function items() { return $this->hasMany(QuoteItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum(function($item) {
            return $item->qty * $item->unit_price;
        });
        $discount = $this->discount ?? 0;
        $taxable = $subtotal - $discount;
        $tax = $taxable * ($this->tax_rate / 100);
        $total = $taxable + $tax;

        $central = 0.0;
        $state = 0.0;
        if ($this->has_subsidy) {
            $kw = 0.0;
            foreach ($this->items as $item) {
                if ($item->product && $item->product->category === 'panel') {
                    $kw += (($item->product->capacity_watts ?? 0) * $item->qty) / 1000.0;
                }
            }
            $subsidyService = resolve(\App\Services\Solar\SubsidyCalculationService::class);
            $subsidyData = $subsidyService->calculate($this->company, $kw);
            $central = $subsidyData['central'];
            $state = $subsidyData['state'];
        }

        $netCost = $this->has_subsidy ? max(0, $total - $central - $state) : $total;

        $this->update([
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'total'           => $total,
            'central_subsidy' => $central,
            'state_subsidy'   => $state,
            'net_cost'        => $netCost,
        ]);
    }

    public static function generateNumber(int $companyId): string
    {
        $last = static::withoutGlobalScopes()->where('company_id', $companyId)->count();
        return 'QT-' . str_pad($companyId, 3, '0', STR_PAD_LEFT) . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
