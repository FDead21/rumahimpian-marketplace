<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'group', 'value'];

    // ----------------------------------------------------------------
    // Query Scopes
    // ----------------------------------------------------------------

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', strtoupper($group));
    }

    // ----------------------------------------------------------------
    // Static Helpers
    // ----------------------------------------------------------------

    /**
     * Get a single setting value.
     * Usage: Setting::get('site_name', 'Default Name')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    /**
     * Get all settings for a group as a key-value array.
     * Cached per group so each group is independently invalidatable.
     *
     * Usage: Setting::group('EO')   → ['eo_site_name' => '...', ...]
     *        Setting::group('GLOBAL') → ['site_name' => '...', ...]
     */
    public static function forGroup(string $group, int $ttl = 3600): array
    {
        $cacheKey = 'settings_group_' . strtolower($group);

        return Cache::remember($cacheKey, $ttl, function () use ($group) {
            return static::where('group', strtoupper($group))
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Get all settings across all groups as a flat key-value array.
     * Used for global View::share. Cached forever — clear on admin save.
     *
     * Usage: Setting::allCached()
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings_all', function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Flush all setting caches. Call this in your Filament observer
     * or admin save hook whenever a setting is updated.
     *
     * Usage: Setting::flushCache()
     */
    public static function flushCache(): void
    {
        Cache::forget('settings_all');
        Cache::forget('settings_group_global');
        Cache::forget('settings_group_property');
        Cache::forget('settings_group_eo');
        Cache::forget('settings_group_system');
    }
}