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
    private array $roles = [];
    private $tempatTugasCache = [];
    private $existingUsernames = [];
    private $existingEmails = [];

    public function __construct()
    {
        $this->roles = Role::all()
            ->flatMap(fn (Role $role) => [
                (string) $role->id_role => $role->id_role,
                strtolower($role->nama_role) => $role->id_role,
            ])
            ->toArray();
        $this->existingUsernames = User::pluck('username', 'username')->map(fn () => true)->toArray();
        $this->existingEmails = User::pluck('email', 'email')->map(fn () => true)->toArray();
    }

    public function collection(Collection $rows)
    {
        $usersToInsert = [];
        $nikData = [];
        $phoneData = [];

        foreach ($rows as $row) {
            $nama = trim((string) ($row['nama'] ?? ''));
            $nik = trim((string) ($row['nik'] ?? ''));
            $username = trim((string) ($row['username'] ?? ''));
            $email = trim((string) ($row['email'] ?? $row['email_opsional'] ?? ''));
            $password = (string) ($row['password'] ?? '');
            $roleInput = trim((string) ($row['role'] ?? 'Petugas PPSU'));
            $statusAktif = strtolower(trim((string) ($row['status_akun'] ?? 'aktif')));
            $noHp = trim((string) ($row['no_telepon_sensitif'] ?? $row['no_telepon'] ?? ''));

            if ($nama === '' || $nik === '' || $username === '' || $email === '' || $password === '' || $roleInput === '') {
                continue;
            }

            if (strlen($password) < 8 || isset($this->existingUsernames[$username]) || isset($this->existingEmails[$email])) {
                continue;
            }

            $roleKey = is_numeric($roleInput) ? $roleInput : strtolower($roleInput);
            if (! isset($this->roles[$roleKey])) {
                continue;
            }

            if (! in_array($statusAktif, ['aktif', 'nonaktif'], true)) {
                $statusAktif = 'aktif';
            }

            $tempatTugas = trim((string) ($row['tempat_tugas'] ?? ''));
            $idTempat = null;
            if ($tempatTugas !== '') {
                if (!isset($this->tempatTugasCache[$tempatTugas])) {
                    $tempat = TempatTugas::where('nama_tempat', $tempatTugas)->first();
                    $this->tempatTugasCache[$tempatTugas] = $tempat?->id_tempat;
                }
                $idTempat = $this->tempatTugasCache[$tempatTugas];
            }

            $this->existingUsernames[$username] = true;
            $this->existingEmails[$email] = true;

            $usersToInsert[] = [
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'id_role' => $this->roles[$roleKey],
                'id_tempat' => $idTempat,
                'regu' => $row['regu'] ?? $row['regu_opsional'] ?? null,
                'shift' => $row['shift'] ?? $row['shift_opsional'] ?? null,
                'status_aktif' => $statusAktif,
                'no_hp' => null,
                'alamat' => $row['alamat'] ?? null,
                'jabatan' => $row['jabatan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $nikData[$username] = $nik;
            $phoneData[$username] = $noHp;
        }

        if (!empty($usersToInsert)) {
            User::insert($usersToInsert);

            $insertedUsernames = array_column($usersToInsert, 'username');
            $insertedUsers = User::whereIn('username', $insertedUsernames)->get()->keyBy('username');

            $sensitiveToInsert = [];
            foreach ($nikData as $username => $nik) {
                if (isset($insertedUsers[$username])) {
                    $row = [
                        'id_user' => $insertedUsers[$username]->id_user,
                        'nik_encrypted' => Crypt::encryptString($nik),
                        'nik_hash' => hash('sha256', $nik),
                        'created_at' => now(),
                    ];

                    if (! empty($phoneData[$username])) {
                        $row['no_hp_encrypted'] = Crypt::encryptString($phoneData[$username]);
                        $row['no_hp_hash'] = hash('sha256', preg_replace('/[^0-9+]/', '', $phoneData[$username]) ?? '');
                    }

                    $sensitiveToInsert[] = $row;
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
