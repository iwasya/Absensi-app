<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Periode extends Model
{
    protected $table = 'periode';
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public static function aktif(): ?self
    {
        return Cache::remember('periode:aktif:' . now()->toDateString(), 300, function () {
            return self::query()
                ->where('status', 'aktif')
                ->where('tanggal_mulai', '<=', now()->toDateString())
                ->where('tanggal_selesai', '>=', now()->toDateString())
                ->orderByDesc('id_periode')
                ->first();
        });
    }

    public static function latestCached()
    {
        return Cache::remember('periode:list:latest', 300, function () {
            return self::query()
                ->orderBy('tanggal_mulai', 'desc')
                ->get();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('periode:aktif:' . now()->toDateString());
        Cache::forget('periode:list:latest');
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
