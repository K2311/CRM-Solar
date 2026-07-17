<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id', 'name', 'category', 'sku', 'description', 'unit_price', 'unit', 'is_active',
    ];

    public function company() { return $this->belongsTo(Company::class); }

    public static function categories(): array
    {
        return ['panel', 'inverter', 'battery', 'mounting', 'accessory', 'service'];
    }

    public static function categoryIcons(): array
    {
        return [
            'panel'     => 'grid-3x3-gap',
            'inverter'  => 'lightning-charge',
            'battery'   => 'battery-full',
            'mounting'  => 'tools',
            'accessory' => 'plug',
            'service'   => 'gear',
        ];
    }
}
