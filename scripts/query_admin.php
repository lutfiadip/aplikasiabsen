<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::where('email', 'admin@example.com')->first();
if (! $u) {
    echo "not found\n";
    exit;
}
echo json_encode($u->toArray(), JSON_PRETTY_PRINT)."\n";