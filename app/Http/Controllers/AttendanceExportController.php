<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceExportController
{
    public function export(Request $request)
    {
        $ids = $request->query('ids', []);

        $attendances = Attendance::whereIn('id', (array) $ids)
            ->with(['user', 'schedule'])
            ->get();

        $headings = [
            'User name',
            'Email address',
            'Schedule name',
            'Attendance date',
            'Check in time',
            'Check out time',
            'Latitude',
            'Longitude',
            'Is late',
            'Notes',
        ];

        $rows = $attendances->map(function ($a) {
            return [
                $a->user?->name ?? $a->user_id,
                $a->user?->email ?? '',
                $a->schedule?->name ?? $a->schedule_id,
                // attendance_date stored as 'YYYY-MM-DD' string
                $a->attendance_date ?? '',
                // check_in_time/check_out_time stored as 'YYYY-MM-DD HH:MM:SS' strings
                $a->check_in_time ? \Carbon\Carbon::parse($a->check_in_time)->toDateTimeString() : '',
                $a->check_out_time ? \Carbon\Carbon::parse($a->check_out_time)->toDateTimeString() : '',
                ($a->latitude !== null ? "'".strval($a->latitude) : ''),
                ($a->longitude !== null ? "'".strval($a->longitude) : ''),
                $a->is_late ? 'Yes' : 'No',
                $a->notes,
            ];
        })->toArray();

        $filename = 'attendances-'.now()->format('Ymd_His');

        // If Laravel-Excel is installed, use it to generate .xlsx
        if (class_exists('\\Maatwebsite\\Excel\\Excel') && class_exists('\\Maatwebsite\\Excel\\Concerns\\FromArray')) {
            // Use an anonymous export class so we don't require a dedicated file
            $export = new class($rows, $headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $rows;
                protected $headings;

                public function __construct(array $rows, array $headings)
                {
                    $this->rows = $rows;
                    $this->headings = $headings;
                }

                public function array(): array
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return $this->headings;
                }
            };

            // Use the facade if available, else resolve the service. Wrap in try/catch to
            // prevent a missing facade or other runtime error from bubbling up and
            // allow falling back to CSV export.
            try {
                if (class_exists('\\Maatwebsite\\Excel\\Facades\\Excel')) {
                    return \Maatwebsite\Excel\Facades\Excel::download($export, $filename.'.xlsx');
                }

                if (app()->bound('excel')) {
                    return app('excel')->download($export, $filename.'.xlsx');
                }
            } catch (\Throwable $e) {
                Log::warning('AttendanceExportController: Excel export failed, falling back to CSV', ['error' => $e->getMessage()]);
            }
        }

        // Fallback: stream a CSV so export works without extra packages
        $csvFilename = $filename.'.csv';

        return response()->streamDownload(function () use ($headings, $rows) {
            // Write UTF-8 BOM for Excel compatibility
            echo "\xEF\xBB\xBF";
            $handle = fopen('php://output', 'w');

            // Use semicolon as delimiter for compatibility with locales where Excel
            // expects ';' as the list separator. Quote all fields to avoid merged
            // cells when values contain commas.
            $delimiter = ';';

            fputcsv($handle, $headings, $delimiter);

            foreach ($rows as $row) {
                fputcsv($handle, $row, $delimiter);
            }

            fclose($handle);
        }, $csvFilename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
