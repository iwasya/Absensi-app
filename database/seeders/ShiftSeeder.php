<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'nama_shift' => 'Shift 1',
                'jam_masuk' => '07:00',
                'jam_pulang' => '15:00',
                'durasi_jam' => 8,
                'warna' => '#3B82F6', // Blue
                'status' => true,
                'urutan' => 1,
            ],
            [
                'nama_shift' => 'Shift 2',
                'jam_masuk' => '15:00',
                'jam_pulang' => '23:00',
                'durasi_jam' => 8,
                'warna' => '#10B981', // Green
                'status' => true,
                'urutan' => 2,
            ],
            [
                'nama_shift' => 'Shift 3',
                'jam_masuk' => '23:00',
                'jam_pulang' => '07:00',
                'durasi_jam' => 8,
                'warna' => '#8B5CF6', // Purple
                'status' => true,
                'urutan' => 3,
            ],
        ];

        foreach ($shifts as $shift) {
            \App\Models\Shift::updateOrCreate(
                ['nama_shift' => $shift['nama_shift']],
                $shift
            );
        }
    }
}
