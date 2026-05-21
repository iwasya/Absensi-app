<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'id_cuti';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'id_pengganti',
        'id_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_cuti',
        'alasan',
        'alasan_lainnya',
        'alamat_cuti',
        'dokumen_path',
        'admin_status',
        'admin_approver_id',
        'admin_processed_at',
        'status',
        'approver_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'admin_processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pengganti(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengganti', 'id_user');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id', 'id_user');
    }

    public function adminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approver_id', 'id_user');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }
}
