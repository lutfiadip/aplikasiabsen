<?php
if ($argc < 2) { echo "Usage: php check_user.php email\n"; exit(1); }
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$email = $argv[1];
$u = App\Models\User::where('email', $email)->first();
if (! $u) { echo "not found\n"; exit; }
echo json_encode($u->toArray(), JSON_PRETTY_PRINT)."\n";
