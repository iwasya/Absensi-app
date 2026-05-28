<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tugas extends Model
{
    protected $table = 'tugas';
    protected $primaryKey = 'id_tugas';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'id_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'uraian',
        'status',
        'submitted_at',
        'is_late_input',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'submitted_at' => 'datetime',
        'is_late_input' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }
}
