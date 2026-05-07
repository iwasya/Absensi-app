<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'tipe',
        'status_baca',
        'reference_id',
        'reference_type',
    ];

    protected $casts = [
        'status_baca' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
