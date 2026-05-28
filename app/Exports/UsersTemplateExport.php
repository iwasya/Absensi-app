<?php

namespace App\Exports;

use App\Models\TempatTugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        // Return contoh data
        return collect([
            [
                1,
                'John Doe',
                '1234567890123456',
                'johndoe',
                'john.doe@example.com',
                'password123',
                'Petugas PPSU',
                'aktif',
                'Kos tirtayasa',
                '081234567890',
                'Petugas',
                'Regu A',
                'Shift 1',
                'Jl. Contoh No. 1',
            ],
            [
                2,
                'Jane Smith',
                '9876543210987654',
                'janesmith',
                'jane.smith@example.com',
                'password123',
                'Petugas PPSU',
                'aktif',
                'Kos tirtayasa',
                '081298765432',
                'Petugas',
                'Regu B',
                'Shift 2',
                'Jl. Contoh No. 2',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIK',
            'Username',
            'Email',
            'Password',
            'Role',
            'Status Akun',
            'Tempat Tugas',
            'No Telepon',
            'Jabatan',
            'Regu (Opsional)',
            'Shift (Opsional)',
            'Alamat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 24,
            'C' => 22,
            'D' => 18,
            'E' => 28,
            'F' => 18,
            'G' => 18,
            'H' => 14,
            'I' => 24,
            'J' => 18,
            'K' => 18,
            'L' => 14,
            'M' => 14,
            'N' => 32,
        ];
    }
}
