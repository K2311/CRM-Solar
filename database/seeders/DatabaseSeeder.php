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

            // Default Role Permissions
            foreach ($allPerms as $perm) {
                // Members get view access
                if (Str::endsWith($perm->name, '.view')) {
                    RolePermission::create([
                        'company_id' => $company->id,
                        'role' => 'member',
                        'permission_id' => $perm->id,
                        'granted' => true,
                    ]);
                }

                // Admins get everything
                RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'admin',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);
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
