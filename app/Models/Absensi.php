<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'id_periode',
        'tanggal',
        'shift',
        'jam_masuk',
        'foto_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'lokasi_masuk',
        'jam_istirahat_mulai',
        'jam_istirahat_selesai',
        'jam_pulang',
        'foto_pulang',
        'latitude_pulang',
        'longitude_pulang',
        'lokasi_pulang',
        'status',
        'keterangan',
        'approval_pulang_status',
        'approval_pulang_requested_at',
        'approval_pulang_forwarded_by',
        'approval_pulang_forwarded_at',
        'approval_pulang_approved_by',
        'approval_pulang_reason',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approval_pulang_requested_at' => 'datetime',
        'approval_pulang_forwarded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }

    public function approvalPulangApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_pulang_approved_by', 'id_user');
    }

    public function approvalPulangForwarder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_pulang_forwarded_by', 'id_user');
    }
}
