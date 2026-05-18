<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';
    protected $primaryKey = 'kunci';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kunci', 'nilai'];

    public static function getNilai($kunci, $default = null)
    {
        $pengaturan = self::find($kunci);
        return $pengaturan ? $pengaturan->nilai : $default;
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('pengaturan:app-shell'));
        static::deleted(fn () => Cache::forget('pengaturan:app-shell'));
    }
}
