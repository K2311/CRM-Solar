<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@solar.com')->first();
if ($user) {
    echo "User found: " . $user->name . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Is Super Admin (casted): " . ($user->is_super_admin ? 'TRUE' : 'FALSE') . "\n";
    echo "Is Super Admin (raw): " . var_export($user->getRawOriginal('is_super_admin'), true) . "\n";
} else {
    echo "User admin@solar.com not found.\n";
}
