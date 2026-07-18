<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions
        $this->call(PermissionSeeder::class);
        $allPerms = Permission::all();

        // 2. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@solar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_super_admin' => true,
        ]);

        // 3. Demo Companies
        $companies = [
            ['name' => 'SolarTech Pvt Ltd', 'email' => 'contact@solartech.com'],
            ['name' => 'GreenPower Solutions', 'email' => 'hello@greenpower.com'],
        ];

        foreach ($companies as $cData) {
            $company = Company::create([
                'name' => $cData['name'],
                'slug' => Str::slug($cData['name']),
                'email' => $cData['email'],
                'timezone' => 'UTC',
                'currency' => 'USD',
            ]);

            // Owner
            $owner = User::create([
                'company_id' => $company->id,
                'name' => $company->name . ' Owner',
                'email' => 'owner@' . Str::slug($company->name) . '.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]);

            // Staff
            User::create([
                'company_id' => $company->id,
                'name' => $company->name . ' Staff',
                'email' => 'staff@' . Str::slug($company->name) . '.com',
                'password' => Hash::make('password'),
                'role' => 'member',
            ]);

            // Sales Exec
            User::create([
                'company_id' => $company->id,
                'name' => 'Sales Executive',
                'email' => 'sales@' . Str::slug($company->name) . '.com',
                'password' => Hash::make('password'),
                'role' => 'sales',
            ]);

            // Field Technician
            User::create([
                'company_id' => $company->id,
                'name' => 'Field Technician',
                'email' => 'tech@' . Str::slug($company->name) . '.com',
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]);

            // Accounts
            User::create([
                'company_id' => $company->id,
                'name' => 'Accounts Officer',
                'email' => 'accounts@' . Str::slug($company->name) . '.com',
                'password' => Hash::make('password'),
                'role' => 'accounts',
            ]);

            // Default Role Permissions
            foreach ($allPerms as $perm) {
                // Admins get everything
                RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'admin',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);

                // Members get view access
                if (Str::endsWith($perm->name, '.view')) {
                    RolePermission::create([
                        'company_id' => $company->id,
                        'role' => 'member',
                        'permission_id' => $perm->id,
                        'granted' => true,
                    ]);
                }

                // Sales: customers, leads, quotes, products.view
                $isSalesPerm = Str::startsWith($perm->name, ['customers', 'leads', 'quotes']) || $perm->name === 'products.view';
                if ($isSalesPerm) {
                    RolePermission::create([
                        'company_id' => $company->id,
                        'role' => 'sales',
                        'permission_id' => $perm->id,
                        'granted' => true,
                    ]);
                }

                // Technician: installations.view, installations.edit, tickets.*, products.view
                $isTechPerm = $perm->name === 'installations.view' || $perm->name === 'installations.edit' || Str::startsWith($perm->name, 'tickets') || $perm->name === 'products.view';
                if ($isTechPerm) {
                    RolePermission::create([
                        'company_id' => $company->id,
                        'role' => 'technician',
                        'permission_id' => $perm->id,
                        'granted' => true,
                    ]);
                }

                // Accounts: payments.*, quotes.view, products.view
                $isAcctsPerm = Str::startsWith($perm->name, 'payments') || $perm->name === 'quotes.view' || $perm->name === 'products.view';
                if ($isAcctsPerm) {
                    RolePermission::create([
                        'company_id' => $company->id,
                        'role' => 'accounts',
                        'permission_id' => $perm->id,
                        'granted' => true,
                    ]);
                }
            }
        }

        // 4. Other Seeders
        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
            MarketingSeeder::class,
        ]);
    }
}
