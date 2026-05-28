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
        'no_hp_encrypted',
        'no_hp_hash',
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

    public function clearNik(): self
    {
        $this->nik_encrypted = null;
        $this->nik_hash = null;
        return $this;
    }

    /**
     * Store the raw phone number encrypted and searchable by hash.
     */
    public function setNoHp(string $rawNoHp): self
    {
        $normalized = $this->normalizePhone($rawNoHp);
        $this->no_hp_encrypted = \Illuminate\Support\Facades\Crypt::encryptString($rawNoHp);
        $this->no_hp_hash = hash('sha256', $normalized);
        return $this;
    }

    public function clearNoHp(): self
    {
        $this->no_hp_encrypted = null;
        $this->no_hp_hash = null;
        return $this;
    }

    private function normalizePhone(string $rawNoHp): string
    {
        return preg_replace('/[^0-9+]/', '', $rawNoHp) ?? '';
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
