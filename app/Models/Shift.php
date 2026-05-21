<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'durasi_jam',
        'warna',
        'status',
        'urutan',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'status' => 'boolean',
        'durasi_jam' => 'integer',
        'urutan' => 'integer',
    ];

    /**
     * Get users assigned to this shift
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'shift', 'nama_shift');
    }

    /**
     * Get formatted jam kerja string
     */
    public function getJamKerjaAttribute(): string
    {
        $masuk = $this->jam_masuk ? \Carbon\Carbon::parse($this->jam_masuk)->format('H:i') : '--:--';
        $pulang = $this->jam_pulang ? \Carbon\Carbon::parse($this->jam_pulang)->format('H:i') : '--:--';
        return "{$masuk} - {$pulang}";
    }

    /**
     * Scope untuk shift yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get jam pulang yang dihitung otomatis dari jam masuk + durasi
     */
    public function calculateJamPulang(): string
    {
        if ($this->jam_masuk && $this->durasi_jam) {
            return \Carbon\Carbon::parse($this->jam_masuk)
                ->addHours($this->durasi_jam)
                ->format('H:i');
        }
        return $this->jam_pulang ? \Carbon\Carbon::parse($this->jam_pulang)->format('H:i') : '--:--';
    }
}