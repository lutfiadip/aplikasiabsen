<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;
$atts = Attendance::orderBy('id','desc')->take(10)->get();
foreach ($atts as $a) {
    echo "ID: {$a->id}\n";
    echo "attendance_date: ".var_export($a->attendance_date, true)."\n";
    echo "check_in_time: ".var_export($a->check_in_time, true)."\n";
    echo "check_out_time: ".var_export($a->check_out_time, true)."\n";
    echo "---\n";
}
