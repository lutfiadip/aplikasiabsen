<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set sample divisions for all anak magang created by MentorSeeder
$mentors = App\Models\User::where('role', App\Models\User::ROLE_PEMBIMBING)->get();
foreach ($mentors as $mentor) {
    App\Models\User::where('mentor_id', $mentor->id)
        ->where('role', App\Models\User::ROLE_ANAK_MAGANG)
        ->update(['division' => 'Development']);
}

// Show affected rows
$rows = App\Models\User::where('role', App\Models\User::ROLE_ANAK_MAGANG)->get(['id','name','email','division']);
echo json_encode($rows->toArray(), JSON_PRETTY_PRINT)."\n";
