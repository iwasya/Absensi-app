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
        'nik_hash',
    ];

    /**
     * Store the raw NIK (decrypted) and auto-generate nik_encrypted + nik_hash.
     * Use this method instead of directly setting nik_encrypted.
     */
    public function setNik(string $rawNik): self
    {
        $this->nik_encrypted = \Illuminate\Support\Facades\Crypt::encryptString($rawNik);
        $this->nik_hash = hash('sha256', $rawNik);
        return $this;
    }

    /**
     * Verify a raw NIK against this record.
     */
    public function verifyNik(string $rawNik): bool
    {
        if (!$this->nik_hash) {
            return false;
        }
        return hash_equals($this->nik_hash, hash('sha256', $rawNik));
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
