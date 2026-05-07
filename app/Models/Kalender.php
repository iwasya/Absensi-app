<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kalender extends Model
{
    protected $table = 'kalender';
    protected $primaryKey = 'id_kalender';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'nama_event',
        'jenis_event',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
