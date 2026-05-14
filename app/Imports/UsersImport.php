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
    private $existingUsernames = [];

    public function __construct()
    {
        $this->rolePetugas = Role::where('nama_role', 'Petugas PPSU')->first();
        $this->existingUsernames = User::pluck('username', 'username')->map(fn () => true)->toArray();
    }

    /**
     * Generate unique username from nama.
     * Format: lowercase nama (slug) + optional suffix if already exists.
     */
    private function generateUsername(string $nama, string $nik): string
    {
        // Generate base username from nama (slug format)
        $parts = explode(' ', trim($nama));
        $firstName = strtolower($parts[0]);
        $lastNameInitial = isset($parts[1]) ? strtolower(substr(end($parts), 0, 1)) : '';

        $baseUsername = $firstName . $lastNameInitial;

        // Ensure minimum 3 characters
        if (strlen($baseUsername) < 3) {
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z]/', '', $nama));
            if (strlen($baseUsername) < 3) {
                // Fallback: use first 6 chars of NIK
                $baseUsername = substr($nik, 0, 6);
            }
        }

        // Check if base username exists
        $username = $baseUsername;
        $counter = 1;

        while (isset($this->existingUsernames[$username])) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $this->existingUsernames[$username] = true;
        return $username;
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

            $nik = trim($row['nik']);
            $nama = trim($row['nama']);

            // Generate unique username from nama (not NIK) - FIRST
            $username = $this->generateUsername($nama, $nik);

            // Generate email from username (not NIK)
            $email = !empty($row['email']) ? $row['email'] : strtolower($username) . '@petugas.local';

            // Skip if email already exists
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $usersToInsert[] = [
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('petugas12345'),
                'id_role' => $this->rolePetugas->id_role,
                'id_tempat' => $this->tempatTugasCache[$row['tempat_tugas']],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $nikData[$username] = $nik;
        }

        if (!empty($usersToInsert)) {
            User::insert($usersToInsert);

            $insertedUsernames = array_column($usersToInsert, 'username');
            $insertedUsers = User::whereIn('username', $insertedUsernames)->get()->keyBy('username');

            $sensitiveToInsert = [];
            foreach ($nikData as $username => $nik) {
                if (isset($insertedUsers[$username])) {
                    $sensitiveToInsert[] = [
                        'id_user' => $insertedUsers[$username]->id_user,
                        'nik_encrypted' => Crypt::encryptString($nik),
                        'nik_hash' => hash('sha256', $nik),
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
