<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

$users = User::all();
foreach ($users as $u) {
    echo $u->id . ' ' . ($u->email ?? '(no email)') . ' ' . ($u->role ?? '(no role)') . PHP_EOL;
}

$admin = User::where('email', 'admin@example.com')->first();
if (! $admin) {
    echo "admin user not found\n";
    exit(0);
}

echo "admin role before: " . ($admin->role ?? '(no role)') . PHP_EOL;
if ($admin->role !== User::ROLE_ADMIN) {
    $admin->role = User::ROLE_ADMIN;
    $admin->save();
    echo "admin role updated to: " . $admin->role . PHP_EOL;
}

echo "Gate viewAny Users: " . (Gate::forUser($admin)->allows('viewAny', User::class) ? 'Y' : 'N') . PHP_EOL;
echo "Gate viewAny Attendances: " . (Gate::forUser($admin)->allows('viewAny', App\Models\Attendance::class) ? 'Y' : 'N') . PHP_EOL;
echo "Gate viewAny Schedules: " . (Gate::forUser($admin)->allows('viewAny', App\Models\Schedule::class) ? 'Y' : 'N') . PHP_EOL;
