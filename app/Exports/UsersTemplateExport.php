<?php

namespace App\Exports;

use App\Models\TempatTugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        // Return contoh data
        return collect([
            [
                'John Doe',
                '1234567890123456',
                'john.doe@example.com',
                'Kos tirtayasa',
            ],
            [
                'Jane Smith',
                '9876543210987654',
                '',
                'Kos tirtayasa',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIK',
            'Email (Opsional)',
            'Tempat Tugas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}