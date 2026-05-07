<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id_log';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'aktivitas',
        'modul',
        'reference_id',
        'reference_type',
        'status',
        'catatan',
        'ip_address',
        'device',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
