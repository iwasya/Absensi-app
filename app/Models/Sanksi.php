<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanksi extends Model
{
    protected $table = 'sanksi';
    protected $primaryKey = 'id_sanksi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis_sanksi',
        'tanggal',
        'keterangan',
        'acknowledged_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'acknowledged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
