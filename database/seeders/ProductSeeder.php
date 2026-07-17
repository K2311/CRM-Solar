<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Mono Perc 550W Panel', 'category' => 'panel', 'unit_price' => 200, 'unit' => 'piece'],
            ['name' => 'Grid-Tie Inverter 5kW', 'category' => 'inverter', 'unit_price' => 1200, 'unit' => 'piece'],
            ['name' => 'LiFePO4 10kWh Battery', 'category' => 'battery', 'unit_price' => 3500, 'unit' => 'piece'],
            ['name' => 'Standard Mounting Kit', 'category' => 'mounting', 'unit_price' => 150, 'unit' => 'set'],
            ['name' => 'Annual Maintenance Plan', 'category' => 'service', 'unit_price' => 500, 'unit' => 'year'],
        ];

        foreach (\App\Models\Company::all() as $company) {
            foreach ($products as $p) {
                Product::create(array_merge($p, [
                    'company_id' => $company->id,
                    'sku' => strtoupper(substr($p['name'], 0, 3)) . '-' . rand(100, 999),
                ]));
            }
        }
    }
}
