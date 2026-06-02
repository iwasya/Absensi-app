<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiburKompensasi extends Model
{
    protected $table = 'libur_kompensasi';
    protected $primaryKey = 'id_libur_kompensasi';

    protected $fillable = [
        'id_user',
        'id_cuti',
        'tanggal_kerja',
        'tanggal_dipakai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kerja' => 'date',
        'tanggal_dipakai' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function cuti(): BelongsTo
    {
        return $this->belongsTo(Cuti::class, 'id_cuti', 'id_cuti');
    }
}
