<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\TempatTugas;
use App\Models\UserSensitive;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private $rolePetugas;
    private $tempatTugasCache = [];
    private $existingUsers = [];

    public function __construct()
    {
        $this->rolePetugas = Role::where('nama_role', 'Petugas PPSU')->first();
        $this->existingUsers = User::pluck('email', 'username')->toArray();
    }

    public function collection(Collection $rows)
    {
        $usersToInsert = [];
        $nikData = [];
        
        foreach ($rows as $row) {
            if (empty($row['nama']) || empty($row['nik']) || empty($row['tempat_tugas'])) {
                continue;
            }

            if (!isset($this->tempatTugasCache[$row['tempat_tugas']])) {
                $tempat = TempatTugas::where('nama_tempat', $row['tempat_tugas'])->first();
                if (!$tempat) {
                    continue;
                }
                $this->tempatTugasCache[$row['tempat_tugas']] = $tempat->id_tempat;
            }

            $email = !empty($row['email']) ? $row['email'] : strtolower(str_replace(' ', '', $row['nik'])) . '@petugas.local';

            if (isset($this->existingUsers[$row['nik']]) || in_array($email, $this->existingUsers)) {
                continue;
            }

            $usersToInsert[] = [
                'nama' => $row['nama'],
                'username' => $row['nik'],
                'email' => $email,
                'password' => Hash::make('petugas12345'),
                'id_role' => $this->rolePetugas->id_role,
                'id_tempat' => $this->tempatTugasCache[$row['tempat_tugas']],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $nikData[$row['nik']] = $row['nik'];
            $this->existingUsers[$row['nik']] = $email;
        }

        if (!empty($usersToInsert)) {
            User::insert($usersToInsert);
            
            $insertedUsernames = array_column($usersToInsert, 'username');
            $insertedUsers = User::whereIn('username', $insertedUsernames)->get()->keyBy('username');
            
            $sensitiveToInsert = [];
            foreach ($nikData as $nik) {
                if (isset($insertedUsers[$nik])) {
                    $sensitiveToInsert[] = [
                        'id_user' => $insertedUsers[$nik]->id_user,
                        'nik_encrypted' => Crypt::encryptString($nik),
                    ];
                }
            }
            
            if (!empty($sensitiveToInsert)) {
                UserSensitive::insert($sensitiveToInsert);
            }
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
