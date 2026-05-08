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

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
