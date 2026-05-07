<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSensitive extends Model
{
    protected $table = 'user_sensitive';
    protected $primaryKey = 'id_sensitive';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'nik_encrypted',
    ];
}
