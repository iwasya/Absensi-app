<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'id_role',
        'id_tempat',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function tempatTugas(): BelongsTo
    {
        return $this->belongsTo(TempatTugas::class, 'id_tempat', 'id_tempat');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_user', 'id_user');
    }

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class, 'id_user', 'id_user');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'id_user', 'id_user');
    }

    public function roleName(): string
    {
        return strtolower((string) optional($this->role)->nama_role);
    }

    public function isAdmin(): bool
    {
        return str_contains($this->roleName(), 'admin');
    }

    public function isAtasan(): bool
    {
        return str_contains($this->roleName(), 'atasan')
            || str_contains($this->roleName(), 'manager')
            || str_contains($this->roleName(), 'menejer');
    }

    public function isPetugas(): bool
    {
        return str_contains($this->roleName(), 'petugas')
            || str_contains($this->roleName(), 'karyawan');
    }

    public function hasRoleAlias(array $aliases): bool
    {
        foreach ($aliases as $alias) {
            if ((string) $this->id_role === (string) $alias) {
                return true;
            }

            if ($alias === 'admin' && $this->isAdmin()) {
                return true;
            }

            if ($alias === 'atasan' && $this->isAtasan()) {
                return true;
            }

            if ($alias === 'petugas' && $this->isPetugas()) {
                return true;
            }
        }

        return false;
    }
}
