<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = \App\Models\Permission::all();
        $companies = \App\Models\Company::all();

        $defaults = [
            'admin' => ['view', 'create', 'edit', 'delete', 'export', 'assign', 'send', 'invite', 'edit_roles', 'remove'], // all
            'member' => ['view', 'create', 'edit'],
            'sales' => ['view', 'create', 'edit', 'send', 'assign'],
            'technician' => ['view', 'edit'],
            'accounts' => ['view', 'export', 'create', 'edit'],
        ];

        foreach ($companies as $company) {
            foreach ($defaults as $role => $allowedActions) {
                // Only insert if no permissions exist for this role yet
                $existing = \App\Models\RolePermission::where('company_id', $company->id)->where('role', $role)->count();
                if ($existing == 0) {
                    foreach ($permissions as $p) {
                        if (in_array($p->action, $allowedActions)) {
                            // some specific exceptions
                            if ($role === 'member' && in_array($p->module, ['settings', 'team'])) continue;
                            if ($role === 'sales' && in_array($p->module, ['settings', 'team'])) continue;
                            if ($role === 'technician' && !in_array($p->module, ['installations', 'service_tickets', 'customers'])) continue;
                            if ($role === 'accounts' && !in_array($p->module, ['payments', 'invoices', 'quotes', 'customers', 'settings'])) continue;
                            
                            \App\Models\RolePermission::create([
                                'company_id' => $company->id,
                                'role' => $role,
                                'permission_id' => $p->id,
                            ]);
                        }
                    }
                }
            }
        }
        $this->command->info('Defaults seeded successfully.');
    }
}
