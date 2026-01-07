<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$path = 'avatars/01KEB7FSD7NH67889W7A70VNSN.webp';
echo app('filesystem')->disk('public')->url($path) . PHP_EOL;