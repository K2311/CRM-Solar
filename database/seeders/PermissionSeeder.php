<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Permission::allModules();

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::updateOrCreate([
                    'name' => "{$module}.{$action}",
                ], [
                    'module' => $module,
                    'action' => $action,
                    'description' => "Allows user to {$action} {$module}",
                ]);
            }
        }
    }
}
