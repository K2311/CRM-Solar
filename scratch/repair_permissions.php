<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;

echo "--- Fixing Admin User ---\n";
$user = User::where('email', 'admin@solar.com')->first();
if ($user) {
    if (!$user->is_super_admin) {
        $user->is_super_admin = true;
        $user->save();
        echo "Set is_super_admin to TRUE for {$user->email}\n";
    } else {
        echo "User {$user->email} is already a Super Admin.\n";
    }
} else {
    echo "ERROR: User admin@solar.com NOT FOUND!\n";
}

echo "\n--- Granting Permissions to 'admin' role ---\n";
$allPerms = Permission::all();
$companies = \App\Models\Company::all();

foreach ($companies as $company) {
    echo "Processing company: {$company->name}\n";
    foreach ($allPerms as $perm) {
        RolePermission::updateOrCreate([
            'company_id' => $company->id,
            'role' => 'admin',
            'permission_id' => $perm->id,
        ], [
            'granted' => true
        ]);
    }
}

echo "\n--- Clearing Cache ---\n";
\Illuminate\Support\Facades\Artisan::call('cache:clear');
\Illuminate\Support\Facades\Artisan::call('view:clear');

echo "Done!\n";
