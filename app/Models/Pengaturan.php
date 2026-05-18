<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'pengaturan:all';

    private static ?array $cachedValues = null;

    protected $table = 'pengaturan';
    protected $primaryKey = 'kunci';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kunci', 'nilai'];

    public static function getNilai($kunci, $default = null)
    {
        $values = self::cachedValues();

        return array_key_exists($kunci, $values) ? $values[$kunci] : $default;
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::forgetCachedValues());
        static::deleted(fn () => self::forgetCachedValues());
    }

    private static function cachedValues(): array
    {
        if (self::$cachedValues !== null) {
            return self::$cachedValues;
        }

        self::$cachedValues = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::query()
                ->pluck('nilai', 'kunci')
                ->all();
        });

        return self::$cachedValues;
    }

    private static function forgetCachedValues(): void
    {
        self::$cachedValues = null;

        Cache::forget(self::CACHE_KEY);
        Cache::forget('pengaturan:app-shell');
    }
}
