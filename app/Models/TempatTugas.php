<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempatTugas extends Model
{
    protected $table = 'tempat_tugas';
    protected $primaryKey = 'id_tempat';
    public $timestamps = false;

    protected $fillable = [
        'nama_tempat',
        'alamat',
        'latitude',
        'longitude',
    ];
}
